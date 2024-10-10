<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Enum;

use Jokod\Impactco2Php\Enum\HeaterEnum;
use PHPUnit\Framework\TestCase;

class HeaterEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('Chauffage au gaz', HeaterEnum::getName(HeaterEnum::GAS_HEATING));
        $this->assertEquals('Chauffage au fioul', HeaterEnum::getName(HeaterEnum::FUEL_OIL_HEATING));
        $this->assertEquals('Chauffage électrique', HeaterEnum::getName(HeaterEnum::ELECTRIC_HEATING));
        $this->assertEquals('Chauffage avec une pompe à chaleur', HeaterEnum::getName(HeaterEnum::HEAT_PUMP_HEATING));
        $this->assertEquals('Chauffage avec un poêle à granulés', HeaterEnum::getName(HeaterEnum::PELLET_STOVE_HEATING));
        $this->assertEquals('Chauffage avec un poêle à bois', HeaterEnum::getName(HeaterEnum::WOOD_STOVE_HEATING));
        $this->assertEquals('Chauffage via un réseau de chaleur', HeaterEnum::getName(HeaterEnum::DISTRICT_HEATING));
        $this->assertEquals('Undefined', HeaterEnum::getName(null));
        $this->assertEquals('Undefined', HeaterEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertEquals('🔥', HeaterEnum::getEmoji(HeaterEnum::GAS_HEATING));
        $this->assertEquals('🛢️', HeaterEnum::getEmoji(HeaterEnum::FUEL_OIL_HEATING));
        $this->assertEquals('⚡', HeaterEnum::getEmoji(HeaterEnum::ELECTRIC_HEATING));
        $this->assertEquals('🌡️', HeaterEnum::getEmoji(HeaterEnum::HEAT_PUMP_HEATING));
        $this->assertEquals('🌾', HeaterEnum::getEmoji(HeaterEnum::PELLET_STOVE_HEATING));
        $this->assertEquals('🌲', HeaterEnum::getEmoji(HeaterEnum::WOOD_STOVE_HEATING));
        $this->assertEquals('🏢', HeaterEnum::getEmoji(HeaterEnum::DISTRICT_HEATING));
        $this->assertEquals('❓', HeaterEnum::getEmoji(null));
        $this->assertEquals('❓', HeaterEnum::getEmoji(999));
    }

    public function testToArray(): void
    {
        $expected = [
            HeaterEnum::GAS_HEATING,
            HeaterEnum::FUEL_OIL_HEATING,
            HeaterEnum::ELECTRIC_HEATING,
            HeaterEnum::HEAT_PUMP_HEATING,
            HeaterEnum::PELLET_STOVE_HEATING,
            HeaterEnum::WOOD_STOVE_HEATING,
            HeaterEnum::DISTRICT_HEATING,
        ];

        $this->assertEquals($expected, HeaterEnum::toArray());
    }
}
