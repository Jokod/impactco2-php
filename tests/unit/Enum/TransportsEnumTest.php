<?php

declare(strict_types = 1);

use Jokod\Impactco2Php\Enum\TransportsEnum;
use PHPUnit\Framework\TestCase;

class TransportsEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Avion', TransportsEnum::getName(TransportsEnum::PLANE));
        $this->assertSame('TGV', TransportsEnum::getName(TransportsEnum::TGV));
        $this->assertSame('Marche', TransportsEnum::getName(TransportsEnum::WALKING));
        $this->assertSame('Non défini', TransportsEnum::getName(null));
        $this->assertSame('Non défini', TransportsEnum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $this->assertSame('✈️', TransportsEnum::getEmoji(TransportsEnum::PLANE));
        $this->assertSame('🚄', TransportsEnum::getEmoji(TransportsEnum::TGV));
        $this->assertSame('🚆', TransportsEnum::getEmoji(TransportsEnum::INTERCITY));
        $this->assertSame('🚗', TransportsEnum::getEmoji(TransportsEnum::CAR));
        $this->assertSame('🚗⚡', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CAR));
        $this->assertSame('🚌', TransportsEnum::getEmoji(TransportsEnum::BUS));
        $this->assertSame('🚴', TransportsEnum::getEmoji(TransportsEnum::BIKE));
        $this->assertSame('🚴⚡', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_BIKE));
        $this->assertSame('🚌', TransportsEnum::getEmoji(TransportsEnum::THERMAL_BUS));
        $this->assertSame('🚊', TransportsEnum::getEmoji(TransportsEnum::TRAMWAY));
        $this->assertSame('🚇', TransportsEnum::getEmoji(TransportsEnum::METRO));
        $this->assertSame('🛵', TransportsEnum::getEmoji(TransportsEnum::SCOOTER));
        $this->assertSame('🏍️', TransportsEnum::getEmoji(TransportsEnum::MOTORCYCLE));
        $this->assertSame('🚇', TransportsEnum::getEmoji(TransportsEnum::RER_TRANSILIEN));
        $this->assertSame('🚆', TransportsEnum::getEmoji(TransportsEnum::TER));
        $this->assertSame('🚌⚡', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_BUS));
        $this->assertSame('🛴⚡', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_SCOOTER));
        $this->assertSame('🚌', TransportsEnum::getEmoji(TransportsEnum::GNV_BUS));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_1));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_2));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_3));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_4));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_1));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_2));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_3));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_4));
        $this->assertSame('🚶', TransportsEnum::getEmoji(TransportsEnum::WALKING));
        $this->assertSame('❓', TransportsEnum::getEmoji(null));
        $this->assertSame('❓', TransportsEnum::getEmoji(999));
    }

    public function testToArray(): void
    {
        $expected = [
            TransportsEnum::PLANE,
            TransportsEnum::TGV,
            TransportsEnum::INTERCITY,
            TransportsEnum::CAR,
            TransportsEnum::ELECTRIC_CAR,
            TransportsEnum::BUS,
            TransportsEnum::BIKE,
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
            TransportsEnum::WALKING,
        ];
        $this->assertSame($expected, TransportsEnum::toArray());
    }
}
