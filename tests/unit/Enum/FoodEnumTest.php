<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Enum;

use Jokod\Impactco2Php\Enum\FoodEnum;
use PHPUnit\Framework\TestCase;

class FoodEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('fruits', FoodEnum::getName(FoodEnum::FRUITS));
        $this->assertEquals('légumes', FoodEnum::getName(FoodEnum::VEGETABLES));
        $this->assertEquals('herbes', FoodEnum::getName(FoodEnum::HERBS));
        $this->assertEquals('pâtes, riz et céréales', FoodEnum::getName(FoodEnum::PASTA_RICE_CEREALS));
        $this->assertEquals('pommes de terre et autres tubercules', FoodEnum::getName(FoodEnum::POTATOES_TUBERS));
        $this->assertEquals('fruits à coque et graines oléagineuses', FoodEnum::getName(FoodEnum::NUTS_SEEDS));
        $this->assertEquals('Undefined', FoodEnum::getName(null));
        $this->assertEquals('Undefined', FoodEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertEquals('🍎', FoodEnum::getEmoji(FoodEnum::FRUITS));
        $this->assertEquals('🥦', FoodEnum::getEmoji(FoodEnum::VEGETABLES));
        $this->assertEquals('🌿', FoodEnum::getEmoji(FoodEnum::HERBS));
        $this->assertEquals('🍚', FoodEnum::getEmoji(FoodEnum::PASTA_RICE_CEREALS));
        $this->assertEquals('🥔', FoodEnum::getEmoji(FoodEnum::POTATOES_TUBERS));
        $this->assertEquals('🌰', FoodEnum::getEmoji(FoodEnum::NUTS_SEEDS));
        $this->assertEquals('❓', FoodEnum::getEmoji(null));
        $this->assertEquals('❓', FoodEnum::getEmoji(999));
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

        $this->assertEquals($expected, FoodEnum::toArray());
    }
}
