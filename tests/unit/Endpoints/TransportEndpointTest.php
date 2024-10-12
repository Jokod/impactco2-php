<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\TransportsEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransportEndpointTest extends TestCase
{
    public function testConstructorWithValidParameters(): void
    {
        $distance = 100;
        $endpoint = new TransportEndpoint($distance);
        $this->assertInstanceOf(TransportEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidTransports(): void
    {
        $distance = 100;
        $transports = [TransportsEnum::CAR, TransportsEnum::PLANE];
        $endpoint = new TransportEndpoint($distance, $transports);
        $this->assertInstanceOf(TransportEndpoint::class, $endpoint);
    }

    public function testConstructorWithAllValidParameters(): void
    {
        $distance = 100;
        $transports = [TransportsEnum::CAR, TransportsEnum::PLANE];
        $displayAll = true;
        $occupencyRate = 2;
        $includeConstruction = 1;
        $ignoreRadiativeForcing = 1;
        $endpoint = new TransportEndpoint($distance, $transports, $displayAll, $occupencyRate, $includeConstruction, $ignoreRadiativeForcing);
        $this->assertInstanceOf(TransportEndpoint::class, $endpoint);
    }

    public function testConstructorWithNegativeDistance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Distance must be a positive integer');
        new TransportEndpoint(-100);
    }

    public function testConstructorWithEmptyTransports(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport list cannot be empty');
        new TransportEndpoint(100, []);
    }

    public function testConstructorWithInvalidTransports(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transport identifier: invalid_transport');
        new TransportEndpoint(100, ['invalid_transport']);
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
        new TransportEndpoint(100, null, false, 1, 2, 0);
    }
}
