<?php


namespace Laradium\Laradium\Documents\Tests;

use Laradium\Laradium\Documents\Services\ParserService;
use Laradium\Laradium\Documents\Tests\Models\Contract;
use ReflectionClass;
use ReflectionException;

class ParserServiceTest extends TestCase
{
    /**
     * @var ParserService
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

        $this->service = new ParserService();

        $this->contract = Contract::create([
            'key'         => 'test',
            'document_id' => $this->document->id
        ]);
    }

    /** @test */
    public function it_returns_documentable_models(): void
    {
        $this->assertIsArray($this->service->getDocumentableModels());
    }

    /** @test */
    public function it_returns_placeholders(): void
    {
        $this->assertIsArray($this->service->getPlaceholders());
    }

    /** @test */
    public function it_returns_flat_placeholders(): void
    {
        $this->assertIsArray($this->service->getFlatPlaceholders());
    }

    /** @test */
    public function it_returns_placeholders_from_models(): void
    {
        $this->assertArrayHasKey('contract', $this->service->getPlaceholders());
    }

    /** @test */
    public function it_returns_flat_placeholders_from_models(): void
    {
        $this->assertContains('contract.key', $this->service->getFlatPlaceholders());
    }

    /** @test
     * @throws ReflectionException
     */
    public function it_returns_placeholder_values(): void
    {
        $values = $this->invokeMethod($this->service, 'getPlaceholderValues', [$this->contract]);

        $this->assertArrayHasKey('app.name', $values);

        $this->assertEquals('Laravel', $values['app.name']);
    }

    /** @test */
    public function it_returns_placeholder_value(): void
    {
        $this->assertEquals('Laravel', $this->invokeMethod($this->service, 'getPlaceholderValue', [
            $this->contract,
            'app.name'
        ]));
    }

    /** @test */
    public function it_returns_documentable_property(): void
    {
        $this->assertEquals('test', $this->invokeMethod($this->service, 'getDocumentableValue', [
            $this->contract,
            'key'
        ]));
    }

    /** @test */
    public function it_runs_function(): void
    {
        $dateTime = $this->invokeMethod($this->service, 'runFunction', ['date_time']);
        $this->assertEquals(now()->toDateTimeString(), $dateTime);

        $date = $this->invokeMethod($this->service, 'runFunction', ['date']);
        $this->assertEquals(now()->toDateString(), $date);

        $dateLong = $this->invokeMethod($this->service, 'runFunction', ['date_long']);
        $this->assertEquals(now()->toFormattedDateString(), $dateLong);

        $time = $this->invokeMethod($this->service, 'runFunction', ['time']);
        $this->assertEquals(now()->toTimeString(), $time);

        $nonExisting = $this->invokeMethod($this->service, 'runFunction', ['test']);
        $this->assertEquals('', $nonExisting);
    }

    /** @test */
    public function it_renders_document(): void
    {
        $this->assertEquals('Test document test', trim($this->service->render($this->contract)));
    }

    /**
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}

