<?php

declare(strict_types = 1);

namespace Tests\Unit\Enum;

use Jokod\Impactco2Php\Enum\AlimentationCategoryEnum;
use PHPUnit\Framework\TestCase;

class AlimentationCategoryEnumTest extends TestCase
{
    public function testToArrayReturnsAllCategories(): void
    {
        $categories = AlimentationCategoryEnum::toArray();

        $this->assertIsArray($categories);
        $this->assertCount(3, $categories);
        $this->assertContains(AlimentationCategoryEnum::GROUP, $categories);
        $this->assertContains(AlimentationCategoryEnum::RAYON, $categories);
        $this->assertContains(AlimentationCategoryEnum::POPULARITY, $categories);
    }

    public function testGetNameReturnsCorrectName(): void
    {
        $this->assertSame('Groupes d\'aliments', AlimentationCategoryEnum::getName(AlimentationCategoryEnum::GROUP));
        $this->assertSame('Rayons du magasin', AlimentationCategoryEnum::getName(AlimentationCategoryEnum::RAYON));
        $this->assertSame('Aliments les plus consommés', AlimentationCategoryEnum::getName(AlimentationCategoryEnum::POPULARITY));
    }

    public function testGetNameReturnsUndefinedForInvalidId(): void
    {
        $this->assertSame('Undefined', AlimentationCategoryEnum::getName('invalid'));
        $this->assertSame('Undefined', AlimentationCategoryEnum::getName(null));
    }

    public function testConstantsHaveCorrectValues(): void
    {
        $this->assertSame('group', AlimentationCategoryEnum::GROUP);
        $this->assertSame('rayon', AlimentationCategoryEnum::RAYON);
        $this->assertSame('popularity', AlimentationCategoryEnum::POPULARITY);
    }
}
