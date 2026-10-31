<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Enum;

use Jokod\Impactco2Php\Enum\HeaterEnum;
use PHPUnit\Framework\TestCase;

class HeaterEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Chauffage au gaz', HeaterEnum::getName(HeaterEnum::GAS_HEATING));
        $this->assertSame('Chauffage au fioul', HeaterEnum::getName(HeaterEnum::FUEL_OIL_HEATING));
        $this->assertSame('Chauffage électrique', HeaterEnum::getName(HeaterEnum::ELECTRIC_HEATING));
        $this->assertSame('Chauffage avec une pompe à chaleur', HeaterEnum::getName(HeaterEnum::HEAT_PUMP_HEATING));
        $this->assertSame('Chauffage avec un poêle à granulés', HeaterEnum::getName(HeaterEnum::PELLET_STOVE_HEATING));
        $this->assertSame('Chauffage avec un poêle à bois', HeaterEnum::getName(HeaterEnum::WOOD_STOVE_HEATING));
        $this->assertSame('Chauffage via un réseau de chaleur', HeaterEnum::getName(HeaterEnum::DISTRICT_HEATING));
        $this->assertSame('Undefined', HeaterEnum::getName(null));
        $this->assertSame('Undefined', HeaterEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertSame('🔥', HeaterEnum::getEmoji(HeaterEnum::GAS_HEATING));
        $this->assertSame('🛢️', HeaterEnum::getEmoji(HeaterEnum::FUEL_OIL_HEATING));
        $this->assertSame('⚡', HeaterEnum::getEmoji(HeaterEnum::ELECTRIC_HEATING));
        $this->assertSame('🌡️', HeaterEnum::getEmoji(HeaterEnum::HEAT_PUMP_HEATING));
        $this->assertSame('🌾', HeaterEnum::getEmoji(HeaterEnum::PELLET_STOVE_HEATING));
        $this->assertSame('🌲', HeaterEnum::getEmoji(HeaterEnum::WOOD_STOVE_HEATING));
        $this->assertSame('🏢', HeaterEnum::getEmoji(HeaterEnum::DISTRICT_HEATING));
        $this->assertSame('❓', HeaterEnum::getEmoji(null));
        $this->assertSame('❓', HeaterEnum::getEmoji(999));
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

        $this->assertSame($expected, HeaterEnum::toArray());
    }
}
