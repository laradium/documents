<?php

namespace Laradium\Laradium\Documents\Tests;

use Illuminate\Http\Response;
use Laradium\Laradium\Documents\Services\DocumentService;
use Laradium\Laradium\Documents\Tests\Models\Contract;

class DocumentServiceTest extends TestCase
{
    /**
     * @var DocumentService
     */
    private $service;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new DocumentService();

        $this->contract = Contract::create([
            'key'         => 'test',
            'document_id' => $this->document->id
        ]);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function it_loads_pdf(): void
    {
        $this->service->pdf($this->contract);
    }

    /** @test */
    public function it_streams_document(): void
    {
        $response = $this->service->pdf($this->contract)->stream();

        $this->assertInstanceOf(Response::class, $response);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertArrayHasKey('content-type', $response->headers->all());

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    /** @test */
    public function it_downloads_document(): void
    {
        $response = $this->service->pdf($this->contract)->download('test');

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertArrayHasKey('content-type', $response->headers->all());
        $this->assertArrayHasKey('content-disposition', $response->headers->all());

        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
        $this->assertContains('attachment; filename="test.pdf"', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function it_saves_document(): void
    {
        if (!is_dir(__DIR__ . '/temp')) {
            mkdir(__DIR__ . '/temp');
        }

        $this->service->pdf($this->contract)->save(__DIR__ . '/temp/test.pdf');

        $this->assertFileExists(__DIR__ . '/temp/test.pdf');

        unlink(__DIR__ . '/temp/test.pdf');
    }

    /** @test */
    public function it_renders_document(): void
    {
        $this->assertEquals('Test document test', trim($this->service->render($this->contract)));
    }
}
