<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersistentFile extends Model
{
    protected $table = 'persistent_files';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'path',
        'mime_type',
        'content',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Pastikan tabel persistent_files tersedia (self-provisioning).
     */
    public static function ensureTableExists(): bool
    {
        try {
            if (!Schema::hasTable('persistent_files')) {
                Schema::create('persistent_files', function (Blueprint $table) {
                    $table->uuid('id')->primary();
                    $table->string('path')->unique();
                    $table->string('mime_type', 100)->default('image/jpeg');
                    $table->longText('content');
                    $table->timestamps();
                    $table->index('path');
                });
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Normalisasi relative path agar konsisten (tanpa prefix public/ atau storage/).
     */
    public static function normalizePath(string $path): string
    {
        $clean = ltrim(str_replace('\\', '/', trim($path)), '/');
        if (Str::startsWith($clean, 'public/')) {
            $clean = substr($clean, 7);
        }
        if (Str::startsWith($clean, 'storage/')) {
            $clean = substr($clean, 8);
        }
        return $clean;
    }

    /**
     * Simpan file dari disk public ke database agar 100% permanen.
     */
    public static function storeFromPublicDisk(string $relativePath): void
    {
        try {
            if (!self::ensureTableExists()) {
                return;
            }

            $cleanPath = self::normalizePath($relativePath);
            if (empty($cleanPath)) {
                return;
            }

            $fullPath = storage_path('app/public/' . $cleanPath);
            if (!is_file($fullPath) || !is_readable($fullPath)) {
                return;
            }

            $binary = @file_get_contents($fullPath);
            if ($binary === false || $binary === '') {
                return;
            }

            $mime = @mime_content_type($fullPath) ?: 'image/jpeg';

            self::updateOrCreate(
                ['path' => $cleanPath],
                [
                    'mime_type' => $mime,
                    'content'   => base64_encode($binary),
                ]
            );
        } catch (\Throwable $e) {
            // Jangan gagalkan transaksi utama jika backup mengalami kendala
        }
    }

    /**
     * Hapus backup dari database ketika file asli sengaja dihapus oleh user.
     */
    public static function deleteByPath(string $relativePath): void
    {
        try {
            if (!Schema::hasTable('persistent_files')) {
                return;
            }
            $cleanPath = self::normalizePath($relativePath);
            self::where('path', $cleanPath)->delete();
        } catch (\Throwable $e) {
            // Abaikan
        }
    }

    /**
     * Pulihkan satu file dari database kembali ke disk lokal (self-healing).
     */
    public static function restoreToPublicDisk(string $relativePath): ?self
    {
        try {
            if (!self::ensureTableExists()) {
                return null;
            }

            $cleanPath = self::normalizePath($relativePath);
            $record = self::where('path', $cleanPath)->first();
            if (!$record || empty($record->content)) {
                return null;
            }

            $binary = base64_decode($record->content, true);
            if ($binary !== false) {
                Storage::disk('public')->put($cleanPath, $binary);
            }

            return $record;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Pulihkan seluruh file dari database ke disk lokal & sinkronkan file lokal yang belum ter-backup.
     */
    public static function syncAndRestoreAll(): void
    {
        try {
            if (!self::ensureTableExists()) {
                return;
            }

            // 1. Sinkronkan file yang sudah ada di disk lokal dari tabel foto_barang, checklist, dan dispute
            $paths = collect();

            if (Schema::hasTable('foto_barang')) {
                $paths = $paths->merge(DB::table('foto_barang')->whereNotNull('path_foto')->pluck('path_foto'));
            }
            if (Schema::hasTable('checklist_serah_terima')) {
                $paths = $paths->merge(DB::table('checklist_serah_terima')->whereNotNull('foto_bukti')->pluck('foto_bukti'));
            }
            if (Schema::hasTable('dispute')) {
                $paths = $paths->merge(DB::table('dispute')->whereNotNull('bukti_foto')->pluck('bukti_foto'));
            }

            $existingBackedUp = self::pluck('path')->flip();

            foreach ($paths->unique() as $rawPath) {
                $clean = self::normalizePath((string) $rawPath);
                if (!empty($clean) && !$existingBackedUp->has($clean)) {
                    self::storeFromPublicDisk($clean);
                }
            }

            // 2. Pulihkan file dari database ke disk jika hilang (misal pasca container restart di Render)
            foreach (self::cursor() as $record) {
                $targetFile = storage_path('app/public/' . $record->path);
                if (!is_file($targetFile)) {
                    $binary = base64_decode($record->content, true);
                    if ($binary !== false) {
                        Storage::disk('public')->put($record->path, $binary);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika database belum siap
        }
    }
}
