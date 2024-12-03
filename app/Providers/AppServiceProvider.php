<?php

namespace App\Providers;

use App\Contract\AbstractPostcodeImporter;
use App\DataSource\ParlvidPostcodeImporter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AbstractPostcodeImporter::class, ParlvidPostcodeImporter::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
