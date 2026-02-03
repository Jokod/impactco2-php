<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Entity\ECV;
use Jokod\Impactco2Php\Enum\ThematicEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ThematicsEcvEndpointTest extends TestCase
{
    public function testConstructorWithValidIdAndDefaultDetail(): void
    {
        $id = ThematicEnum::CLOTHING;
        $endpoint = new ThematicsEcvEndpoint($id);
        $this->assertInstanceOf(ThematicsEcvEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidIdAndDetailOne(): void
    {
        $id = ThematicEnum::DRINK;
        $detail = 1;
        $endpoint = new ThematicsEcvEndpoint($id, $detail);
        $this->assertInstanceOf(ThematicsEcvEndpoint::class, $endpoint);
    }

    public function testConstructorWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid thematic ECV identifier or slug');
        new ThematicsEcvEndpoint(-1);
    }

    public function testConstructorWithValidSlug(): void
    {
        $endpoint = new ThematicsEcvEndpoint('mobilier');
        $this->assertInstanceOf(ThematicsEcvEndpoint::class, $endpoint);
        $path = $endpoint->getPath('fr');
        $this->assertStringContainsString('mobilier', $path);
    }

    public function testConstructorWithInvalidSlug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid thematic ECV identifier or slug');
        new ThematicsEcvEndpoint('slug_invalide');
    }

    public function testConstructorWithInvalidDetail(): void
    {
        $id = ThematicEnum::FRUITS_AND_VEGETABLES;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Detail must be 0 or 1');
        new ThematicsEcvEndpoint($id, 2);
    }

    public function testTransformResponseHydratesEcv(): void
    {
        $endpoint = new ThematicsEcvEndpoint(ThematicEnum::FURNITURE);
        $raw = [
            'data' => [
                'name'      => 'Meubles',
                'ecv'       => 100.5,
                'slug'      => 'meubles',
                'footprint' => 95.2,
                'items'     => [['id' => 1, 'value' => 50.0]],
                'usage'     => ['perYear' => 1.0, 'defaultYears' => 10],
                'endOfLife' => 5.3,
            ],
            'warning' => null,
        ];
        $response = $endpoint->transformResponse($raw);

        $data = $response->getData();
        $this->assertInstanceOf(ECV::class, $data);
        $this->assertSame('Meubles', $data->getName());
        $this->assertSame(100.5, $data->getEcv());
        $this->assertSame('meubles', $data->getSlug());
        $this->assertSame(95.2, $data->getFootprint());
        $this->assertSame(5.3, $data->getEndOfLife());
    }

    public function testTransformResponseWithEmptyDataReturnsRawData(): void
    {
        $endpoint = new ThematicsEcvEndpoint(ThematicEnum::FURNITURE);
        $raw = ['data' => [], 'warning' => 'Aucune donnée'];
        $response = $endpoint->transformResponse($raw);

        $this->assertSame([], $response->getData());
        $this->assertSame('Aucune donnée', $response->getWarning());
    }

    public function testTransformResponseWithMissingOrNullDataReturnsEmptyArray(): void
    {
        $endpoint = new ThematicsEcvEndpoint(ThematicEnum::FURNITURE);
        $raw = ['data' => null, 'warning' => null];
        $response = $endpoint->transformResponse($raw);

        // $raw['data'] ?? [] donne [] quand data est null, donc getData() retourne [] (pas d'hydratation ECV)
        $this->assertSame([], $response->getData());
        $this->assertNull($response->getWarning());
    }
}
