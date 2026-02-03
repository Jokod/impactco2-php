<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Entity\Thematic;
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

    public function testTransformResponseHydratesThematics(): void
    {
        $endpoint = new ThematicsEndpoint();
        $raw = [
            'data' => [
                ['id' => 1, 'name' => 'Transport', 'slug' => 'transport'],
                ['id' => 2, 'name' => 'Alimentation', 'slug' => 'alimentation'],
            ],
            'warning' => null,
        ];
        $response = $endpoint->transformResponse($raw);

        $data = $response->getData();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertInstanceOf(Thematic::class, $data[0]);
        $this->assertSame(1, $data[0]->getId());
        $this->assertSame('Transport', $data[0]->getName());
        $this->assertSame('transport', $data[0]->getSlug());
    }

    public function testTransformResponseWithNonArrayDataReturnsEmptyArray(): void
    {
        $endpoint = new ThematicsEndpoint();
        $raw = ['data' => 'not-an-array', 'warning' => null];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertNull($response->getWarning());
    }

    public function testTransformResponseWithWarning(): void
    {
        $endpoint = new ThematicsEndpoint();
        $raw = ['data' => [], 'warning' => 'Message d\'avertissement'];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertSame('Message d\'avertissement', $response->getWarning());
    }
}
