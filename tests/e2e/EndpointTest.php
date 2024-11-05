<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\E2E;

use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Endpoints\FruitsVegetables;
use Jokod\Impactco2Php\Endpoints\HeaterEndpoint;
use Jokod\Impactco2Php\Endpoints\ThematicsEndpoint;
use Jokod\Impactco2Php\Endpoints\TransportEndpoint;
use Jokod\Impactco2Php\Enum\FoodEnum;
use Jokod\Impactco2Php\Enum\HeaterEnum;
use Jokod\Impactco2Php\Enum\TransportsEnum;
use PHPUnit\Framework\TestCase;

/**
 * @group e2e
 */
class EndpointTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'api_key' => getenv('IMPACTCO2_API_KEY') ?: null,
            'language' => 'fr'
        ]);
    }

    /**
     * @test
     * @dataProvider provideEndpointConfigurations
     */
    public function itShouldHandleVariousEndpointConfigurations(callable $endpointFactory, array $expectedKeys): void
    {
        $endpoint = $endpointFactory();
        $result = $this->client->execute($endpoint);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $item) {
            foreach ($expectedKeys as $key) {
                $this->assertArrayHasKey($key, $item);
            }
        }
    }

    public function provideEndpointConfigurations(): array
    {
        return [
            'transport' => [
                fn() => new TransportEndpoint(100, [TransportsEnum::CAR]),
                ['id', 'name', 'value']
            ],
            'heater' => [
                fn() => new HeaterEndpoint(100, [HeaterEnum::ELECTRIC_HEATING]),
                ['id', 'name', 'value']
            ],
            'thematics' => [
                fn() => new ThematicsEndpoint(),
                ['id', 'name', 'slug']
            ]
        ];
    }

    /**
     * @test
     */
    public function itShouldHandleTransportEndpointWithAllParameters(): void
    {
        $endpoint = new TransportEndpoint(
            100,
            [TransportsEnum::CAR],
            true,
            2,
            1,
            1
        );

        $result = $this->client->execute($endpoint);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @test
     */
    public function itShouldHandleHeaterEndpointWithAllParameters(): void
    {
        $endpoint = new HeaterEndpoint(
            100,
            [HeaterEnum::ELECTRIC_HEATING, HeaterEnum::GAS_HEATING]
        );

        $result = $this->client->execute($endpoint);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @test
     */
    public function itShouldHandleFruitsVegetablesEndpointWithAllParameters(): void
    {
        $endpoint = new FruitsVegetables(
            5,
            [FoodEnum::FRUITS, FoodEnum::VEGETABLES]
        );

        $result = $this->client->execute($endpoint);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
} 