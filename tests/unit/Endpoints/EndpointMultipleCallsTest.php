<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use PHPUnit\Framework\TestCase;

class EndpointMultipleCallsTest extends TestCase
{
    public function testGetPathCalledTwiceReturnsConsistentResults(): void
    {
        $endpoint = new Endpoint('base/endpoint', ['param1'], ['key1' => 'value1']);
        
        $firstCall = $endpoint->getPath('en');
        $secondCall = $endpoint->getPath('en');
        
        $this->assertSame($firstCall, $secondCall, 'Multiple calls to getPath() should return the same result');
        $this->assertSame('base/endpoint/param1?key1=value1', $firstCall);
    }

    public function testGetPathWithDifferentLanguagesIsConsistent(): void
    {
        $endpoint = new Endpoint('base/endpoint', [], ['language' => null, 'key' => 'value']);
        
        $firstCall = $endpoint->getPath('fr');
        $secondCall = $endpoint->getPath('en');
        
        // Les deux devraient avoir une structure cohérente
        $this->assertStringContainsString('language=fr', $firstCall);
        $this->assertStringContainsString('language=en', $secondCall);
    }
}
