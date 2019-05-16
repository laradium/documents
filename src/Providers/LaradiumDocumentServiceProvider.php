<?php

namespace Laradium\Laradium\Documents\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Laradium\Laradium\Registries\FieldRegistry;

class LaradiumDocumentServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishAssets();
        $this->loadViews();
        $this->loadMigrations();

        // Global helpers
        require_once __DIR__ . '/../Helpers/Global.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerFields();
    }

    /**
     * @return void
     */
    private function publishConfig(): void
    {
        $configPath = __DIR__ . '/../../config/laradium-documents.php';
        $this->mergeConfigFrom($configPath, 'laradium-documents');

        $this->publishes([
            $configPath => config_path('laradium-documents.php'),
        ], 'laradium-documents');
    }

    /**
     * @return void
     */
    private function publishAssets(): void
    {
        $assetPath = __DIR__ . '/../../public/laradium/assets/js/documents.js';

        $tags = [
            'laradium', 'laradium-assets', 'laradium-documents'
        ];

        foreach ($tags as $tag) {
            $this->publishes([
                $assetPath => public_path('laradium/assets/js/documents.js')
            ], $tag);
        }
    }

    /**
     * @return void
     */
    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium-documents');
    }

    /**
     * @return void
     */
    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * @return void
     */
    private function registerFields(): void
    {
        $fieldRegistry = app(FieldRegistry::class);

        foreach (File::allFiles(__DIR__ . '/../Base/Fields') as $path) {
            $field = $path->getPathname();
            $baseName = basename($field, '.php');
            $field = 'Laradium\\Laradium\\Documents\\Base\\Fields\\' . $baseName;
            $fieldList[lcfirst($baseName)] = $field;

            $fieldRegistry->register(lcfirst($baseName), $field);
        }
    }
}
