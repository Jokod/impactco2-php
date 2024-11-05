<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\HeaterEndpoint;
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

    public function testConstructorWithNegativeSurface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Surface must be a positive integer');
        new HeaterEndpoint(-10);
    }

    public function testConstructorWithZeroSurface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Surface must be a positive integer');
        new HeaterEndpoint(0);
    }

    public function testConstructorWithEmptyTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Heating type list cannot be empty');
        new HeaterEndpoint(100, []);
    }

    public function testConstructorWithSingleInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type of heating: invalid1');
        new HeaterEndpoint(100, ['invalid1']);
    }

    public function testConstructorWithMultipleInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type of heating: invalid1, invalid2');
        new HeaterEndpoint(100, ['invalid1', 'invalid2']);
    }

    public function testConstructorWithValidParameters(): void
    {
        $surface = 100;
        $types = [HeaterEnum::ELECTRIC_HEATING, HeaterEnum::GAS_HEATING];
        $endpoint = new HeaterEndpoint($surface, $types);
        $this->assertInstanceOf(HeaterEndpoint::class, $endpoint);
    }

    public function testGetPathWithAllParameters(): void
    {
        $endpoint = new HeaterEndpoint(100, [HeaterEnum::ELECTRIC_HEATING, HeaterEnum::GAS_HEATING]);
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('chauffage', $path);
        $this->assertStringContainsString('m2=100', $path);
        $this->assertStringContainsString('chauffages=' . HeaterEnum::ELECTRIC_HEATING . ',' . HeaterEnum::GAS_HEATING, $path);
        $this->assertStringContainsString('language=fr', $path);
    }

    public function testGetPathWithoutOptionalParameters(): void
    {
        $endpoint = new HeaterEndpoint();
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('chauffage', $path);
        $this->assertStringNotContainsString('m2=', $path);
        $this->assertStringNotContainsString('chauffages=', $path);
        $this->assertStringContainsString('language=fr', $path);
    }
}
