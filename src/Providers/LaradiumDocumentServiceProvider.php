<?php

namespace Laradium\Laradium\Documents\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Laradium\Laradium\Registries\FieldRegistry;
use Laradium\Laradium\Services\Asset\AssetManager;

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
        $this->loadRoutes();

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
        $this->registerAssets();
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
        $assets = [
            __DIR__ . '/../../public/laradium/assets/js/documents.js'  => public_path('laradium/assets/js/documents/documents.js'),
            __DIR__ . '/../../public/laradium/assets/js/components.js' => public_path('laradium/assets/js/documents/components.js'),
        ];

        $tags = [
            'laradium', 'laradium-assets', 'laradium-documents'
        ];

        foreach ($tags as $tag) {
            $this->publishes($assets, $tag);
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
    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
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

    /**
     * @return void
     */
    private function registerAssets(): void
    {
        $assetManager = app(AssetManager::class);
        $assetManager->js()->beforeCore([
            versionedAsset('laradium/assets/js/documents/components.js')
        ]);
    }
}
