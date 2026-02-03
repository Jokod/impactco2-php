<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\ApiResponse;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{
    public function testGetPathWithoutParamsAndQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint');
        $result = $endpoint->getPath('en');
        $this->assertSame('base/endpoint', $result);
    }

    public function testGetPathWithParams(): void
    {
        $endpoint = new Endpoint('base/endpoint', ['param1', 'param2']);
        $result = $endpoint->getPath('en');
        $this->assertSame('base/endpoint/param1/param2', $result);
    }

    public function testGetPathWithQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', [], ['key1' => 'value1', 'key2' => 'value2']);
        $result = $endpoint->getPath('en');
        $this->assertSame('base/endpoint?key1=value1&key2=value2', $result);
    }

    public function testGetPathWithParamsAndQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', ['param1'], ['key1' => 'value1']);
        $result = $endpoint->getPath('en');
        $this->assertSame('base/endpoint/param1?key1=value1', $result);
    }

    public function testGetPathWithLanguageInQuery(): void
    {
        $endpoint = new Endpoint('base/endpoint', [], ['language' => 'fr']);
        $result = $endpoint->getPath('en');
        $this->assertSame('base/endpoint?language=en', $result);
    }

    public function testTransformResponseReturnsApiResponseWithDataAndWarning(): void
    {
        $endpoint = new Endpoint('base/endpoint');
        $raw = ['data' => [1, 2, 3], 'warning' => 'Message'];
        $response = $endpoint->transformResponse($raw);

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertSame([1, 2, 3], $response->getData());
        $this->assertSame('Message', $response->getWarning());
    }

    public function testTransformResponseWithMissingKeys(): void
    {
        $endpoint = new Endpoint('base/endpoint');
        $response = $endpoint->transformResponse([]);

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertSame([], $response->getData());
        $this->assertNull($response->getWarning());
    }
}
