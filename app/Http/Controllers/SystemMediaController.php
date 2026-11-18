<?php

namespace App\Http\Controllers;

use App\Models\PersistentFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDO;

class SystemMediaController extends Controller
{
    /**
     * Endpoint ringan untuk Keep-Alive / Health-Check.
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status'    => 'alive',
            'app'       => config('app.name', 'Rentiz'),
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }

    /**
     * One-Click Cloud Database Setup (untuk platform serverless seperti Vercel tanpa SSH).
     * Otomatis membuat database target di TiDB Cloud, menjalankan migrate & seeder.
     */
    public function setupDatabase(): JsonResponse
    {
        try {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', '4000');
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $db   = config('database.connections.mysql.database', 'rentiz');
            $opts = config('database.connections.mysql.options', []);

            // 1. Pastikan database target (misal: rentiz) sudah terbuat di TiDB Cloud
            $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, $opts);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $safeDb = preg_replace('/[^a-zA-Z0-9_]/', '', $db) ?: 'rentiz';
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Reconnect Laravel ke database target & jalankan migrasi + seeder
            DB::purge('mysql');
            DB::reconnect('mysql');

            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = trim(Artisan::output());

            Artisan::call('db:seed', ['--force' => true]);
            $seedOutput = trim(Artisan::output());

            // 3. Pastikan tabel persistent_files siap
            PersistentFile::syncAndRestoreAll();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Database cloud berhasil disiapkan! Semua tabel, 11 kategori, dan akun admin sudah aktif.',
                'database' => $safeDb,
                'migrate'  => $migrateOutput,
                'seed'     => $seedOutput,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Self-Healing Storage Fallback:
     * Jika file di public/storage/{path} hilang (karena container/serverless restart),
     * otomatis pulihkan dari tabel persistent_files di database ke disk sementara,
     * lalu kirimkan gambar ke browser tanpa error.
     */
    public function serveStorage(string $path)
    {
        $cleanPath = PersistentFile::normalizePath($path);

        // Cegah directory traversal
        if (strpos($cleanPath, '..') !== false) {
            abort(404);
        }

        // 1. Cek apakah file sudah ada di storage/app/public
        $localFullPath = storage_path('app/public/' . $cleanPath);
        if (is_file($localFullPath) && is_readable($localFullPath)) {
            PersistentFile::storeFromPublicDisk($cleanPath);

            $mime = @mime_content_type($localFullPath) ?: 'image/jpeg';
            return response()->file($localFullPath, [
                'Content-Type'  => $mime,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }

        // 2. Jika hilang dari disk lokal / serverless Vercel, pulihkan dari database (Self-Healing)
        $record = PersistentFile::restoreToPublicDisk($cleanPath);
        if ($record && !empty($record->content)) {
            $binary = base64_decode($record->content, true);
            if ($binary !== false) {
                return response($binary, 200, [
                    'Content-Type'  => $record->mime_type ?: 'image/jpeg',
                    'Cache-Control' => 'public, max-age=31536000',
                ]);
            }
        }

        abort(404);
    }
}
