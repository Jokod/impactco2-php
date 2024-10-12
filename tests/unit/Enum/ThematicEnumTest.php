<?php

declare(strict_types = 1);

use Jokod\Impactco2Php\Enum\ThematicEnum;
use PHPUnit\Framework\TestCase;

class ThematicEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Numeric', ThematicEnum::getName(ThematicEnum::NUMERIC));
        $this->assertSame('Meal', ThematicEnum::getName(ThematicEnum::MEAL));
        $this->assertSame('Drink', ThematicEnum::getName(ThematicEnum::DRINK));
        $this->assertSame('Transport', ThematicEnum::getName(ThematicEnum::TRANSPORT));
        $this->assertSame('Clothing', ThematicEnum::getName(ThematicEnum::CLOTHING));
        $this->assertSame('Appliance', ThematicEnum::getName(ThematicEnum::APPLIANCE));
        $this->assertSame('Furniture', ThematicEnum::getName(ThematicEnum::FURNITURE));
        $this->assertSame('Heating', ThematicEnum::getName(ThematicEnum::HEATING));
        $this->assertSame('Fruits and Vegetables', ThematicEnum::getName(ThematicEnum::FRUITS_AND_VEGETABLES));
        $this->assertSame('Digital Usage', ThematicEnum::getName(ThematicEnum::DIGITAL_USAGE));
        $this->assertSame('Case Studies', ThematicEnum::getName(ThematicEnum::CASE_STUDIES));
        $this->assertSame('Undefined', ThematicEnum::getName(null));
        $this->assertSame('Undefined', ThematicEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertSame('💻', ThematicEnum::getEmoji(ThematicEnum::NUMERIC));
        $this->assertSame('🍽️', ThematicEnum::getEmoji(ThematicEnum::MEAL));
        $this->assertSame('🍹', ThematicEnum::getEmoji(ThematicEnum::DRINK));
        $this->assertSame('🚗', ThematicEnum::getEmoji(ThematicEnum::TRANSPORT));
        $this->assertSame('👗', ThematicEnum::getEmoji(ThematicEnum::CLOTHING));
        $this->assertSame('🔌', ThematicEnum::getEmoji(ThematicEnum::APPLIANCE));
        $this->assertSame('🛋️', ThematicEnum::getEmoji(ThematicEnum::FURNITURE));
        $this->assertSame('🔥', ThematicEnum::getEmoji(ThematicEnum::HEATING));
        $this->assertSame('🍎', ThematicEnum::getEmoji(ThematicEnum::FRUITS_AND_VEGETABLES));
        $this->assertSame('📱', ThematicEnum::getEmoji(ThematicEnum::DIGITAL_USAGE));
        $this->assertSame('📚', ThematicEnum::getEmoji(ThematicEnum::CASE_STUDIES));
        $this->assertSame('❓', ThematicEnum::getEmoji(null));
        $this->assertSame('❓', ThematicEnum::getEmoji(999));
    }

    public function testToArray(): void
    {
        $expected = [
            ThematicEnum::NUMERIC,
            ThematicEnum::MEAL,
            ThematicEnum::DRINK,
            ThematicEnum::TRANSPORT,
            ThematicEnum::CLOTHING,
            ThematicEnum::APPLIANCE,
            ThematicEnum::FURNITURE,
            ThematicEnum::HEATING,
            ThematicEnum::FRUITS_AND_VEGETABLES,
            ThematicEnum::DIGITAL_USAGE,
            ThematicEnum::CASE_STUDIES,
        ];
        $this->assertSame($expected, ThematicEnum::toArray());
    }
}
