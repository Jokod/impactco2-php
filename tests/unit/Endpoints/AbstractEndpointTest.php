<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\AbstractEndpoint;
use PHPUnit\Framework\TestCase;

class AbstractEndpointTest extends TestCase
{
    private TestAbstractEndpoint $endpoint;

    protected function setUp(): void
    {
        $this->endpoint = new TestAbstractEndpoint();
    }

    public function testGetPathWithoutParamsAndQuery(): void
    {
        $this->assertSame('test', $this->endpoint->getPath('fr'));
    }

    public function testGetPathWithParams(): void
    {
        $endpoint = new TestAbstractEndpoint('test', ['param1', 'param2']);
        $this->assertSame('test/param1/param2', $endpoint->getPath('fr'));
    }

    public function testGetPathWithQuery(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], ['key' => 'value']);
        $this->assertSame('test?key=value', $endpoint->getPath('fr'));
    }

    public function testGetPathWithLanguageInQuery(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], ['language' => null]);
        $this->assertSame('test?language=fr', $endpoint->getPath('fr'));
    }

    public function testGetPathWithMultipleQueryParameters(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], [
            'param1' => 'value1',
            'param2' => 'value2',
            'language' => null
        ]);

        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('param1=value1', $path);
        $this->assertStringContainsString('param2=value2', $path);
        $this->assertStringContainsString('language=fr', $path);
    }

    public function testGetPathWithSpecialCharacters(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], ['param' => 'value with spaces']);
        $this->assertStringContainsString('param=value+with+spaces', $endpoint->getPath('fr'));
    }

    public function testGetPathWithEmptyParams(): void
    {
        $endpoint = new TestAbstractEndpoint('test', []);
        $this->assertSame('test', $endpoint->getPath('fr'));
    }

    public function testGetPathWithEmptyQuery(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], []);
        $this->assertSame('test', $endpoint->getPath('fr'));
    }

    public function testGetPathWithNullQueryValues(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], [
            'param1' => null,
            'param2' => 'value2'
        ]);

        $path = $endpoint->getPath('fr');

        $this->assertStringNotContainsString('param1', $path);
        $this->assertStringContainsString('param2=value2', $path);
    }

    public function testGetPathWithDifferentLanguages(): void
    {
        $endpoint = new TestAbstractEndpoint('test', [], ['language' => null]);

        $this->assertStringContainsString('language=fr', $endpoint->getPath('fr'));
        $this->assertStringContainsString('language=en', $endpoint->getPath('en'));
        $this->assertStringContainsString('language=es', $endpoint->getPath('es'));
    }
}

class TestAbstractEndpoint extends AbstractEndpoint
{
    public function __construct(string $endpoint = 'test', array $params = [], array $query = [])
    {
        parent::__construct($endpoint, $params, $query);
    }
} 