<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Enum;

use Jokod\Impactco2Php\Enum\FoodEnum;
use PHPUnit\Framework\TestCase;

class FoodEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('fruits', FoodEnum::getName(FoodEnum::FRUITS));
        $this->assertSame('légumes', FoodEnum::getName(FoodEnum::VEGETABLES));
        $this->assertSame('herbes', FoodEnum::getName(FoodEnum::HERBS));
        $this->assertSame('pâtes, riz et céréales', FoodEnum::getName(FoodEnum::PASTA_RICE_CEREALS));
        $this->assertSame('pommes de terre et autres tubercules', FoodEnum::getName(FoodEnum::POTATOES_TUBERS));
        $this->assertSame('fruits à coque et graines oléagineuses', FoodEnum::getName(FoodEnum::NUTS_SEEDS));
        $this->assertSame('Undefined', FoodEnum::getName(null));
        $this->assertSame('Undefined', FoodEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertSame('🍎', FoodEnum::getEmoji(FoodEnum::FRUITS));
        $this->assertSame('🥦', FoodEnum::getEmoji(FoodEnum::VEGETABLES));
        $this->assertSame('🌿', FoodEnum::getEmoji(FoodEnum::HERBS));
        $this->assertSame('🍚', FoodEnum::getEmoji(FoodEnum::PASTA_RICE_CEREALS));
        $this->assertSame('🥔', FoodEnum::getEmoji(FoodEnum::POTATOES_TUBERS));
        $this->assertSame('🌰', FoodEnum::getEmoji(FoodEnum::NUTS_SEEDS));
        $this->assertSame('❓', FoodEnum::getEmoji(null));
        $this->assertSame('❓', FoodEnum::getEmoji(999));
    }

    public function testToArray(): void
    {
        $expected = [
            FoodEnum::FRUITS,
            FoodEnum::VEGETABLES,
            FoodEnum::HERBS,
            FoodEnum::PASTA_RICE_CEREALS,
            FoodEnum::POTATOES_TUBERS,
            FoodEnum::NUTS_SEEDS,
        ];

        $this->assertSame($expected, FoodEnum::toArray());
    }
}
