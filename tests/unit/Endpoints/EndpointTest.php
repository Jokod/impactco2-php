<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\Endpoint;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    private TestEndpoint $endpoint;

    protected function setUp(): void
    {
        $this->endpoint = new TestEndpoint();
    }

    public function testGetPathWithoutParamsAndQuery(): void
    {
        $this->assertSame('test', $this->endpoint->getPath('fr'));
    }

    public function testGetPathWithParams(): void
    {
        $endpoint = new TestEndpoint('test', ['param1', 'param2']);
        $this->assertSame('test/param1/param2', $endpoint->getPath('fr'));
    }

    public function testGetPathWithQuery(): void
    {
        $endpoint = new TestEndpoint('test', [], ['key' => 'value']);
        $this->assertSame('test?key=value', $endpoint->getPath('fr'));
    }

    public function testGetPathWithLanguageInQuery(): void
    {
        $endpoint = new TestEndpoint('test', [], ['language' => null]);
        $this->assertSame('test?language=fr', $endpoint->getPath('fr'));
    }

    public function testGetPathWithNullQueryValues(): void
    {
        $endpoint = new TestEndpoint('test', [], ['key1' => null, 'key2' => 'value2']);
        $this->assertSame('test?key2=value2', $endpoint->getPath('fr'));
    }

    public function testGetPathWithArrayQueryValues(): void
    {
        $endpoint = new TestEndpoint('test', [], ['items' => [1, 2, 3]]);
        $this->assertSame('test?items=1,2,3', $endpoint->getPath('fr'));
    }

    public function testGetPathWithBooleanQueryValues(): void
    {
        $endpoint = new TestEndpoint('test', [], ['flag' => true]);
        $this->assertSame('test?flag=1', $endpoint->getPath('fr'));
    }

    public function testGetPathWithMultipleQueryParameters(): void
    {
        $endpoint = new TestEndpoint('test', [], [
            'param1' => 'value1',
            'param2' => [1, 2],
            'param3' => true,
            'language' => null
        ]);
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('param1=value1', $path);
        $this->assertStringContainsString('param2=1,2', $path);
        $this->assertStringContainsString('param3=1', $path);
        $this->assertStringContainsString('language=fr', $path);
    }

    public function testGetPathWithSpecialCharacters(): void
    {
        $endpoint = new TestEndpoint('test', [], ['param' => 'value with spaces']);
        $this->assertStringContainsString('param=value+with+spaces', $endpoint->getPath('fr'));
    }
}

class TestEndpoint extends Endpoint
{
    public function __construct(string $endpoint = 'test', array $params = [], array $query = [])
    {
        parent::__construct($endpoint, $params, $query);
    }
}
