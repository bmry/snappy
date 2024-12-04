<?php

namespace App\Providers;

use App\PostCodeImporter\AbstractPostcodeImporter;
use App\PostCodeImporter\ParlvidPostcodeImporter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
