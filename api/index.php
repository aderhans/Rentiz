<?php

// Supress deprecation notice PHP 8.2/8.3 untuk kompatibilitas paket vendor Laravel 8
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// Pastikan direktori writable di /tmp tersedia pada runtime serverless Vercel
$tmpStorage = '/tmp/storage';
foreach (['/app/public', '/framework/cache/data', '/framework/sessions', '/framework/views', '/logs'] as $dir) {
    if (!is_dir($tmpStorage . $dir)) {
        @mkdir($tmpStorage . $dir, 0777, true);
    }
}

// Teruskan request ke entry point standar Laravel
require __DIR__ . '/../public/index.php';
