<?php

declare(strict_types = 1);

namespace Tests\Unit\Endpoints;

use Jokod\Impactco2Php\Endpoints\AlimentationEndpoint;
use Jokod\Impactco2Php\Enum\AlimentationCategoryEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AlimentationEndpointTest extends TestCase
{
    public function testConstructorWithValidGroupCategory(): void
    {
        $endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::GROUP);
        $this->assertInstanceOf(AlimentationEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidRayonCategory(): void
    {
        $endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::RAYON);
        $this->assertInstanceOf(AlimentationEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidPopularityCategory(): void
    {
        $endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::POPULARITY);
        $this->assertInstanceOf(AlimentationEndpoint::class, $endpoint);
    }

    public function testConstructorWithInvalidCategory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category. Must be one of: group, rayon, popularity');

        new AlimentationEndpoint('invalid_category');
    }

    public function testGetPathReturnsCorrectEndpoint(): void
    {
        $endpoint = new AlimentationEndpoint(AlimentationCategoryEnum::GROUP);
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('alimentation', $path);
        $this->assertStringContainsString('category=group', $path);
    }

    public function testGetPathWithDifferentCategories(): void
    {
        $endpointGroup = new AlimentationEndpoint(AlimentationCategoryEnum::GROUP);
        $pathGroup = $endpointGroup->getPath('fr');
        $this->assertStringContainsString('category=group', $pathGroup);

        $endpointRayon = new AlimentationEndpoint(AlimentationCategoryEnum::RAYON);
        $pathRayon = $endpointRayon->getPath('fr');
        $this->assertStringContainsString('category=rayon', $pathRayon);

        $endpointPopularity = new AlimentationEndpoint(AlimentationCategoryEnum::POPULARITY);
        $pathPopularity = $endpointPopularity->getPath('fr');
        $this->assertStringContainsString('category=popularity', $pathPopularity);
    }
}
