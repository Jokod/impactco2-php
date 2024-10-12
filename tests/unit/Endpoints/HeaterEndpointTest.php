<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\HeaterEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HeaterEndpointTest extends TestCase
{
    public function testConstructorWithDefaultParameters(): void
    {
        $endpoint = new HeaterEndpoint();
        $this->assertInstanceOf(HeaterEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidParameters(): void
    {
        $surface = 100;
        $types = [HeaterEnum::DISTRICT_HEATING, HeaterEnum::GAS_HEATING];
        $endpoint = new HeaterEndpoint($surface, $types);
        $this->assertInstanceOf(HeaterEndpoint::class, $endpoint);
    }

    public function testConstructorWithNegativeSurface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Surface must be a positive integer');
        new HeaterEndpoint(-10);
    }

    public function testConstructorWithEmptyTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Heating type list cannot be empty');
        new HeaterEndpoint(100, []);
    }

    public function testConstructorWithInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type of heating: invalid_type');
        new HeaterEndpoint(100, ['invalid_type']);
    }
}
