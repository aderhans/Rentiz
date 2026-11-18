#!/bin/bash
set -e

# Sesuaikan port Apache jika host (seperti Render) menyediakan environment variable $PORT
if [ ! -z "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Pastikan symlink storage public tersedia
php artisan storage:link || true

# Jalankan migrasi database & pulihkan foto dari database secara otomatis saat booting
if [ ! -z "$DB_HOST" ]; then
    echo "🛠️ Memastikan skema database tersedia di Cloud..."
    php -r "
        \$host = getenv('DB_HOST');
        \$port = getenv('DB_PORT') ?: '4000';
        \$user = getenv('DB_USERNAME');
        \$pass = getenv('DB_PASSWORD');
        \$db   = getenv('DB_DATABASE') ?: 'test';
        \$opts = file_exists('/etc/ssl/certs/ca-certificates.crt') ? [PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt'] : [];
        try {
            \$pdo = new PDO(\"mysql:host={\$host};port={\$port}\", \$user, \$pass, \$opts);
            \$pdo->exec(\"CREATE DATABASE IF NOT EXISTS \`{\$db}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci\");
        } catch (Throwable \$e) {}
    " || true

    echo "⚡ Menjalankan database migrations & seeder..."
    php artisan migrate --force || true
    php artisan db:seed --force || true

    echo "🖼️ Memulihkan foto permanen dari database ke storage..."
    php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); \App\Models\PersistentFile::syncAndRestoreAll();" || true
fi

# Optimasi cache konfigurasi & view (route:clear digunakan karena ada Closure di web.php)
php artisan config:cache || true
php artisan route:clear || true
php artisan view:cache || true

# Pastikan permission folder storage aman setelah restore
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "🚀 Rentiz Web Server siap menerima traffic..."
exec apache2-foreground
