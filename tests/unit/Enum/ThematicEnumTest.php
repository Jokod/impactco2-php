<?php

declare(strict_types = 1);

use Jokod\Impactco2Php\Enum\ThematicEnum;
use PHPUnit\Framework\TestCase;

class ThematicEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('Numeric', ThematicEnum::getName(ThematicEnum::NUMERIC));
        $this->assertEquals('Meal', ThematicEnum::getName(ThematicEnum::MEAL));
        $this->assertEquals('Drink', ThematicEnum::getName(ThematicEnum::DRINK));
        $this->assertEquals('Transport', ThematicEnum::getName(ThematicEnum::TRANSPORT));
        $this->assertEquals('Clothing', ThematicEnum::getName(ThematicEnum::CLOTHING));
        $this->assertEquals('Appliance', ThematicEnum::getName(ThematicEnum::APPLIANCE));
        $this->assertEquals('Furniture', ThematicEnum::getName(ThematicEnum::FURNITURE));
        $this->assertEquals('Heating', ThematicEnum::getName(ThematicEnum::HEATING));
        $this->assertEquals('Fruits and Vegetables', ThematicEnum::getName(ThematicEnum::FRUITS_AND_VEGETABLES));
        $this->assertEquals('Digital Usage', ThematicEnum::getName(ThematicEnum::DIGITAL_USAGE));
        $this->assertEquals('Case Studies', ThematicEnum::getName(ThematicEnum::CASE_STUDIES));
        $this->assertEquals('Undefined', ThematicEnum::getName(null));
        $this->assertEquals('Undefined', ThematicEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertEquals('💻', ThematicEnum::getEmoji(ThematicEnum::NUMERIC));
        $this->assertEquals('🍽️', ThematicEnum::getEmoji(ThematicEnum::MEAL));
        $this->assertEquals('🍹', ThematicEnum::getEmoji(ThematicEnum::DRINK));
        $this->assertEquals('🚗', ThematicEnum::getEmoji(ThematicEnum::TRANSPORT));
        $this->assertEquals('👗', ThematicEnum::getEmoji(ThematicEnum::CLOTHING));
        $this->assertEquals('🔌', ThematicEnum::getEmoji(ThematicEnum::APPLIANCE));
        $this->assertEquals('🛋️', ThematicEnum::getEmoji(ThematicEnum::FURNITURE));
        $this->assertEquals('🔥', ThematicEnum::getEmoji(ThematicEnum::HEATING));
        $this->assertEquals('🍎', ThematicEnum::getEmoji(ThematicEnum::FRUITS_AND_VEGETABLES));
        $this->assertEquals('📱', ThematicEnum::getEmoji(ThematicEnum::DIGITAL_USAGE));
        $this->assertEquals('📚', ThematicEnum::getEmoji(ThematicEnum::CASE_STUDIES));
        $this->assertEquals('❓', ThematicEnum::getEmoji(null));
        $this->assertEquals('❓', ThematicEnum::getEmoji(999));
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
        $this->assertEquals($expected, ThematicEnum::toArray());
    }
}
