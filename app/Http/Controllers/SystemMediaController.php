<?php

namespace App\Http\Controllers;

use App\Models\PersistentFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SystemMediaController extends Controller
{
    /**
     * Endpoint ringan untuk Keep-Alive / Cron-job / UptimeRobot
     * agar server cloud Render tidak masuk mode sleep (spin-down).
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
     * Self-Healing Storage Fallback:
     * Jika file di public/storage/{path} hilang (karena container Render restart),
     * otomatis pulihkan dari tabel persistent_files di database ke disk lokal,
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
            // Pastikan juga sudah ter-backup di database
            PersistentFile::storeFromPublicDisk($cleanPath);

            $mime = @mime_content_type($localFullPath) ?: 'image/jpeg';
            return response()->file($localFullPath, [
                'Content-Type'  => $mime,
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }

        // 2. Jika hilang dari disk lokal, pulihkan dari database (Self-Healing)
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
