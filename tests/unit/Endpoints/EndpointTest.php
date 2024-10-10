<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    public function testGetPathWithoutParamsAndQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint');
        $result = $endpoint->getPath('en');
        $this->assertEquals('base/endpoint', $result);
    }

    public function testGetPathWithParams(): void
    {
        $endpoint = new Endpoint('base/endpoint', ['param1', 'param2']);
        $result = $endpoint->getPath('en');
        $this->assertEquals('base/endpoint/param1/param2', $result);
    }

    public function testGetPathWithQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', [], ['key1' => 'value1', 'key2' => 'value2']);
        $result = $endpoint->getPath('en');
        $this->assertEquals('base/endpoint?key1=value1&key2=value2', $result);
    }

    public function testGetPathWithParamsAndQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', ['param1'], ['key1' => 'value1']);
        $result = $endpoint->getPath('en');
        $this->assertEquals('base/endpoint/param1?key1=value1', $result);
    }

    public function testGetPathWithLanguageInQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', [], ['language' => 'fr']);
        $result = $endpoint->getPath('en');
        $this->assertEquals('base/endpoint?language=en', $result);
    }
}
