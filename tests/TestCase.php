<?php

namespace Laradium\Laradium\Documents\Tests;

use Barryvdh\DomPDF\ServiceProvider;
use CreateDocumentsTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Laradium\Laradium\Documents\Models\Document;
use Laradium\Laradium\Documents\Providers\LaradiumDocumentServiceProvider;
use Laradium\Laradium\Documents\Tests\Models\Contract;
use Laradium\Laradium\Providers\LaradiumServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @var Document
     */
    public $document;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            LaradiumServiceProvider::class,
            LaradiumDocumentServiceProvider::class
        ];
    }

    /**
     * Set up the environment.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('laradium-documents.models', [
            Contract::class
        ]);

        $app['config']->set('cache.prefix', 'laradium_tests---');
    }

    /**
     * Set up the database.
     *
     * @param Application $app
     */
    protected function setUpDatabase($app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->softDeletes();
        });

        include_once __DIR__ . '/../database/migrations/2019_05_08_000000_create_documents_table.php';

        (new CreateDocumentsTable())->up();

        $app['db']->connection()->getSchemaBuilder()->create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable();
            documentColumns($table);
            $table->timestamps();
        });

        $this->document = Document::create([
            'user_id'  => auth()->id(),
            'key'      => sha1(uniqid('', true)),
            'revision' => 1,
            'type'     => Document::TYPE_CURRENT,
            'title'    => 'Test',
            'content'  => file_get_contents(__DIR__ . '/resources/stubs/test-document.stub')
        ]);
    }
}
