<?php

namespace Laradium\Laradium\Documents\Providers;

use Illuminate\Support\ServiceProvider;

class LaradiumDocumentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/laradium-documents.php';
        $assetPath = __DIR__ . '/../../public/laradium';
        $this->mergeConfigFrom($configPath, 'laradium-documents');

        $this->publishes([
            $configPath => config_path('laradium-documents.php'),
            $assetPath  => public_path('laradium')
        ], 'laradium-documents');

        $this->publishes([
            $assetPath  => public_path('laradium')
        ], 'laradium');

        $this->publishes([
            $assetPath  => public_path('laradium')
        ], 'laradium-assets');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium-documents');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Global helpers
        require_once __DIR__ . '/../Helpers/Global.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }
}
