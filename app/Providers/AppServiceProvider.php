<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set Carbon locale ke Bahasa Indonesia
        Carbon::setLocale('id');
        setlocale(LC_ALL, 'id_ID.utf8', 'id_ID', 'id');

        // Paksa skema HTTPS ketika berjalan di cloud proxy (seperti Render.com)
        if ($this->app->environment('production') || request()->header('x-forwarded-proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Sinkronisasi & pemulihan otomatis foto ke/dari database (1x per jam via cache)
        if (!$this->app->runningInConsole()) {
            try {
                \Illuminate\Support\Facades\Cache::remember('rentiz_persistent_files_sync', 3600, function () {
                    \App\Models\PersistentFile::syncAndRestoreAll();
                    return true;
                });
            } catch (\Throwable $e) {
                // Abaikan jika database belum terhubung
            }
        }
    }
}
