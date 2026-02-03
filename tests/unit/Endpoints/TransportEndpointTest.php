<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Entity\Transport;
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
        $this->expectExceptionMessage('Invalid transport identifier: 999');
        new TransportEndpoint(100, [999]);
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

    public function testTransformResponseHydratesTransports(): void
    {
        $endpoint = new TransportEndpoint(100);
        $raw = [
            'data' => [
                ['id' => 1, 'name' => 'Voiture', 'value' => 22.5],
                ['id' => 2, 'name' => 'TGV', 'value' => 2.5],
            ],
            'warning' => null,
        ];
        $response = $endpoint->transformResponse($raw);

        $data = $response->getData();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertInstanceOf(Transport::class, $data[0]);
        $this->assertSame(1, $data[0]->getId());
        $this->assertSame('Voiture', $data[0]->getName());
        $this->assertSame(22.5, $data[0]->getValue());
    }

    public function testTransformResponseWithNonArrayDataReturnsEmptyArray(): void
    {
        $endpoint = new TransportEndpoint(100);
        $raw = ['data' => 'not-an-array', 'warning' => null];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertNull($response->getWarning());
    }

    public function testTransformResponseWithWarning(): void
    {
        $endpoint = new TransportEndpoint(100);
        $raw = ['data' => [], 'warning' => 'Avertissement distance'];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertSame('Avertissement distance', $response->getWarning());
    }

    public function testTransformResponseWithMissingWarningKeyReturnsNullWarning(): void
    {
        $endpoint = new TransportEndpoint(100);
        $raw = ['data' => [['id' => 1, 'name' => 'Car', 'value' => 10.0]]];
        $response = $endpoint->transformResponse($raw);

        $this->assertCount(1, $response->getData());
        $this->assertNull($response->getWarning());
    }

    public function testTransformResponseWithWarningNotStringReturnsNullWarning(): void
    {
        $endpoint = new TransportEndpoint(100);
        $raw = ['data' => [], 'warning' => 123];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertNull($response->getWarning());
    }
}
