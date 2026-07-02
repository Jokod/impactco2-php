<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

class TransportsEnum
{
    public const PLANE = 1;
    public const TGV = 2;
    public const INTERCITY = 3;
    public const CAR = 4;
    public const ELECTRIC_CAR = 5;
    public const BUS = 6;
    public const BIKE = 7;
    public const ELECTRIC_BIKE = 8;
    public const THERMAL_BUS = 9;
    public const TRAMWAY = 10;
    public const METRO = 11;
    public const SCOOTER = 12;
    public const MOTORCYCLE = 13;
    public const RER_TRANSILIEN = 14;
    public const TER = 15;
    public const ELECTRIC_BUS = 16;
    public const ELECTRIC_SCOOTER = 17;
    public const GNV_BUS = 21;
    public const CARPOOLING_1 = 22;
    public const CARPOOLING_2 = 23;
    public const CARPOOLING_3 = 24;
    public const CARPOOLING_4 = 25;
    public const ELECTRIC_CARPOOLING_1 = 26;
    public const ELECTRIC_CARPOOLING_2 = 27;
    public const ELECTRIC_CARPOOLING_3 = 28;
    public const ELECTRIC_CARPOOLING_4 = 29;
    public const WALKING = 30;
    public const CAMPER_VAN = 31;
    public const LIGHT_MOTORCYCLE = 32;
    public const ELECTRIC_MOPED = 33;
    public const CARGO_BIKE = 34;
    public const VAN = 35;

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::PLANE                 => 'Avion',
        self::TGV                   => 'TGV',
        self::INTERCITY             => 'Intercités',
        self::CAR                   => 'Voiture',
        self::ELECTRIC_CAR          => 'Voiture électrique',
        self::BUS                   => 'Bus',
        self::BIKE                  => 'Vélo',
        self::ELECTRIC_BIKE         => 'Vélo à assistance électrique',
        self::THERMAL_BUS           => 'Bus thermique',
        self::TRAMWAY               => 'Tramway',
        self::METRO                 => 'Métro',
        self::SCOOTER               => 'Scooter',
        self::MOTORCYCLE            => 'Moto',
        self::RER_TRANSILIEN        => 'RER Transilien',
        self::TER                   => 'TER',
        self::ELECTRIC_BUS          => 'Bus électrique',
        self::ELECTRIC_SCOOTER      => 'Scooter électrique',
        self::GNV_BUS               => 'Bus GNV',
        self::CARPOOLING_1          => 'Covoiturage 1 personne',
        self::CARPOOLING_2          => 'Covoiturage 2 personnes',
        self::CARPOOLING_3          => 'Covoiturage 3 personnes',
        self::CARPOOLING_4          => 'Covoiturage 4 personnes',
        self::ELECTRIC_CARPOOLING_1 => 'Covoiturage électrique 1 personne',
        self::ELECTRIC_CARPOOLING_2 => 'Covoiturage électrique 2 personnes',
        self::ELECTRIC_CARPOOLING_3 => 'Covoiturage électrique 3 personnes',
        self::ELECTRIC_CARPOOLING_4 => 'Covoiturage électrique 4 personnes',
        self::WALKING               => 'Marche',
        self::CAMPER_VAN            => 'Camping-car',
        self::LIGHT_MOTORCYCLE      => 'Moto thermique (<= 250 cm³)',
        self::ELECTRIC_MOPED        => 'Scooter électrique',
        self::CARGO_BIKE            => 'Vélo cargo triporteur',
        self::VAN                   => 'Van',
    ];

    public static function getName(?int $id): string
    {
        if (!isset(self::$names[$id])) {
            return 'Non défini';
        }

        return self::$names[$id];
    }

    /**
     * Get the emoji for a type
     *
     * @param int|null $id
     *
     * @return string
     */
    public static function getEmoji(?int $id): string
    {
        switch ($id) {
            case self::PLANE:
                return '✈️';
            case self::TGV:
                return '🚄';
            case self::INTERCITY:
                return '🚆';
            case self::CAR:
                return '🚗';
            case self::ELECTRIC_CAR:
                return '🚗⚡';
            case self::BUS:
                return '🚌';
            case self::BIKE:
                return '🚴';
            case self::ELECTRIC_BIKE:
                return '🚴⚡';
            case self::THERMAL_BUS:
                return '🚌';
            case self::TRAMWAY:
                return '🚊';
            case self::METRO:
                return '🚇';
            case self::SCOOTER:
                return '🛵';
            case self::MOTORCYCLE:
                return '🏍️';
            case self::RER_TRANSILIEN:
                return '🚇';
            case self::TER:
                return '🚆';
            case self::ELECTRIC_BUS:
                return '🚌⚡';
            case self::ELECTRIC_SCOOTER:
                return '🛴⚡';
            case self::GNV_BUS:
                return '🚌';
            case self::CARPOOLING_1:
                return '🚗👥';
            case self::CARPOOLING_2:
                return '🚗👥';
            case self::CARPOOLING_3:
                return '🚗👥';
            case self::CARPOOLING_4:
                return '🚗👥';
            case self::ELECTRIC_CARPOOLING_1:
                return '🚗⚡👥';
            case self::ELECTRIC_CARPOOLING_2:
                return '🚗⚡👥';
            case self::ELECTRIC_CARPOOLING_3:
                return '🚗⚡👥';
            case self::ELECTRIC_CARPOOLING_4:
                return '🚗⚡👥';
            case self::WALKING:
                return '🚶';
            case self::CAMPER_VAN:
                return '🚐';
            case self::LIGHT_MOTORCYCLE:
                return '🏍️';
            case self::ELECTRIC_MOPED:
                return '🛵⚡';
            case self::CARGO_BIKE:
                return '🚲';
            case self::VAN:
                return '🚐';
            default:
                return '❓';
        }
    }

    /**
     * @return int[] array of types
     */
    public static function toArray(): array
    {
        return [
            self::PLANE,
            self::TGV,
            self::INTERCITY,
            self::CAR,
            self::ELECTRIC_CAR,
            self::BUS,
            self::BIKE,
            self::ELECTRIC_BIKE,
            self::THERMAL_BUS,
            self::TRAMWAY,
            self::METRO,
            self::SCOOTER,
            self::MOTORCYCLE,
            self::RER_TRANSILIEN,
            self::TER,
            self::ELECTRIC_BUS,
            self::ELECTRIC_SCOOTER,
            self::GNV_BUS,
            self::CARPOOLING_1,
            self::CARPOOLING_2,
            self::CARPOOLING_3,
            self::CARPOOLING_4,
            self::ELECTRIC_CARPOOLING_1,
            self::ELECTRIC_CARPOOLING_2,
            self::ELECTRIC_CARPOOLING_3,
            self::ELECTRIC_CARPOOLING_4,
            self::WALKING,
            self::CAMPER_VAN,
            self::LIGHT_MOTORCYCLE,
            self::ELECTRIC_MOPED,
            self::CARGO_BIKE,
            self::VAN,
        ];
    }
}
