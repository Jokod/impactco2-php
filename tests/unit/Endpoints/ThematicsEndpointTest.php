<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use PHPUnit\Framework\TestCase;

class ThematicsEndpointTest extends TestCase
{
    public function testConstructorInitializesObject(): void
    {
        $endpoint = new ThematicsEndpoint();
        $this->assertInstanceOf(ThematicsEndpoint::class, $endpoint);
    }

    public function testEndpointIsSetCorrectly(): void
    {
        $endpoint = new ThematicsEndpoint();
        $this->assertSame('thematiques', $endpoint::ENDPOINT);
    }
}
