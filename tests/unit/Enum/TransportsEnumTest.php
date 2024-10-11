<?php

declare(strict_types = 1);

use Jokod\Impactco2Php\Enum\TransportsEnum;
use PHPUnit\Framework\TestCase;

class TransportsEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('Avion', TransportsEnum::getName(TransportsEnum::AVION));
        $this->assertEquals('TGV', TransportsEnum::getName(TransportsEnum::TGV));
        $this->assertEquals('Non défini', TransportsEnum::getName(null));
        $this->assertEquals('Non défini', TransportsEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertEquals('✈️', TransportsEnum::getEmoji(TransportsEnum::AVION));
        $this->assertEquals('🚄', TransportsEnum::getEmoji(TransportsEnum::TGV));
        $this->assertEquals('❓', TransportsEnum::getEmoji(null));
        $this->assertEquals('❓', TransportsEnum::getEmoji(999));
    }

    public function testToArray(): void
    {
        $expected = [
            TransportsEnum::AVION,
            TransportsEnum::TGV,
            TransportsEnum::INTERCITES,
            TransportsEnum::CAR,
            TransportsEnum::ELECTRIC_CAR,
            TransportsEnum::BUS,
            TransportsEnum::ON_FOOT,
            TransportsEnum::ELECTRIC_BIKE,
            TransportsEnum::THERMAL_BUS,
            TransportsEnum::TRAMWAY,
            TransportsEnum::METRO,
            TransportsEnum::SCOOTER,
            TransportsEnum::MOTORCYCLE,
            TransportsEnum::RER_TRANSILIEN,
            TransportsEnum::TER,
            TransportsEnum::ELECTRIC_BUS,
            TransportsEnum::ELECTRIC_SCOOTER,
            TransportsEnum::GNV_BUS,
            TransportsEnum::CARPOOLING_1,
            TransportsEnum::CARPOOLING_2,
            TransportsEnum::CARPOOLING_3,
            TransportsEnum::CARPOOLING_4,
            TransportsEnum::ELECTRIC_CARPOOLING_1,
            TransportsEnum::ELECTRIC_CARPOOLING_2,
            TransportsEnum::ELECTRIC_CARPOOLING_3,
            TransportsEnum::ELECTRIC_CARPOOLING_4,
        ];
        $this->assertEquals($expected, TransportsEnum::toArray());
    }
}
