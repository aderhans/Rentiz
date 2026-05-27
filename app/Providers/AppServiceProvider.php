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
    }
}
