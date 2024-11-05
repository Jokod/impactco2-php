<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\TransportEndpoint;
use Jokod\Impactco2Php\Enum\TransportsEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransportEndpointTest extends TestCase
{
    public function testConstructorWithNegativeDistance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Distance must be a positive integer');
        new TransportEndpoint(-100);
    }

    public function testConstructorWithZeroDistance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Distance must be a positive integer');
        new TransportEndpoint(0);
    }

    public function testConstructorWithEmptyTransports(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport list cannot be empty');
        new TransportEndpoint(100, []);
    }

    public function testConstructorWithSingleInvalidTransport(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transport identifier: invalid1');
        new TransportEndpoint(100, ['invalid1']);
    }

    public function testConstructorWithMultipleInvalidTransports(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transport identifier: invalid1, invalid2');
        new TransportEndpoint(100, ['invalid1', 'invalid2']);
    }

    public function testConstructorWithNegativeOccupencyRate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Occupency rate must be a positive integer');
        new TransportEndpoint(100, null, false, -1);
    }

    public function testConstructorWithInvalidIgnoreRadiativeForcing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ignore radiative forcing must be 0 or 1');
        new TransportEndpoint(100, null, false, 1, 0, 2);
    }

    public function testConstructorWithInvalidIncludeConstruction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Include construction must be 0 or 1');
        new TransportEndpoint(100, null, false, 1, 2);
    }

    public function testConstructorWithValidParameters(): void
    {
        $endpoint = new TransportEndpoint(
            100,
            [TransportsEnum::CAR],
            true,
            2,
            1,
            1
        );
        $this->assertInstanceOf(TransportEndpoint::class, $endpoint);
    }

    public function testGetPathWithAllParameters(): void
    {
        $endpoint = new TransportEndpoint(
            100,
            [TransportsEnum::CAR],
            true,
            2,
            1,
            1
        );
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('transport', $path);
        $this->assertStringContainsString('km=100', $path);
        $this->assertStringContainsString('displayAll=1', $path);
        $this->assertStringContainsString('transports=' . TransportsEnum::CAR, $path);
        $this->assertStringContainsString('occupencyRate=2', $path);
        $this->assertStringContainsString('includeConstruction=1', $path);
        $this->assertStringContainsString('ignoreRadiativeForcing=1', $path);
        $this->assertStringContainsString('language=fr', $path);
    }

    public function testGetPathWithMinimalParameters(): void
    {
        $endpoint = new TransportEndpoint(100);
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('transport', $path);
        $this->assertStringContainsString('km=100', $path);
        $this->assertStringContainsString('displayAll=0', $path);
        $this->assertStringNotContainsString('transports=', $path);
        $this->assertStringContainsString('occupencyRate=1', $path);
        $this->assertStringContainsString('includeConstruction=0', $path);
        $this->assertStringContainsString('ignoreRadiativeForcing=0', $path);
        $this->assertStringContainsString('language=fr', $path);
    }
}
