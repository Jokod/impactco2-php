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
        $this->assertSame('Camping-car', TransportsEnum::getName(TransportsEnum::CAMPER_VAN));
        $this->assertSame('Van', TransportsEnum::getName(TransportsEnum::VAN));
        $this->assertSame('Voiture - Petite - Essence', TransportsEnum::getName(TransportsEnum::CAR_SMALL_PETROL));
        $this->assertSame('Voiture hybride (5 personnes)', TransportsEnum::getName(TransportsEnum::CAR_HYBRID_5));
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
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_1));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_2));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_3));
        $this->assertSame('🚗👥', TransportsEnum::getEmoji(TransportsEnum::CARPOOLING_4));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_1));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_2));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_3));
        $this->assertSame('🚗⚡👥', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_CARPOOLING_4));
        $this->assertSame('🚶', TransportsEnum::getEmoji(TransportsEnum::WALKING));
        $this->assertSame('🚐', TransportsEnum::getEmoji(TransportsEnum::CAMPER_VAN));
        $this->assertSame('🏍️', TransportsEnum::getEmoji(TransportsEnum::LIGHT_MOTORCYCLE));
        $this->assertSame('🛵⚡', TransportsEnum::getEmoji(TransportsEnum::ELECTRIC_MOPED));
        $this->assertSame('🚲', TransportsEnum::getEmoji(TransportsEnum::CARGO_BIKE));
        $this->assertSame('🚐', TransportsEnum::getEmoji(TransportsEnum::VAN));
        $this->assertSame('🚗', TransportsEnum::getEmoji(TransportsEnum::CAR_SMALL_PETROL));
        $this->assertSame('🚗🔋', TransportsEnum::getEmoji(TransportsEnum::CAR_HYBRID));
        $this->assertSame('❓', TransportsEnum::getEmoji(null));
        $this->assertSame('❓', TransportsEnum::getEmoji(999));
    }

    public function testToArrayCoversAllOpenApiIds(): void
    {
        $ids = TransportsEnum::toArray();
        $this->assertCount(136, $ids);
        $this->assertContains(TransportsEnum::PLANE, $ids);
        $this->assertContains(TransportsEnum::CAR_SMALL_PETROL, $ids);
        $this->assertContains(TransportsEnum::CAR_HYBRID_5, $ids);
        $this->assertSame($ids, array_values(array_unique($ids)));
    }
}
