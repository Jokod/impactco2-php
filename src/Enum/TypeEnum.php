<?php

namespace Jokod\Impactco2Php\Enum;

class TypeEnum
{
    public const AVION = 1;
    public const TGV = 2;
    public const INTERCITES = 3;
    public const CAR = 4;
    public const ELECTRIC_CAR = 5;
    public const BUS = 6;
    public const ON_FOOT = 7;
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

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::AVION                 => 'Avion',
        self::TGV                   => 'TGV',
        self::INTERCITES            => 'Intercités',
        self::CAR                   => 'Voiture',
        self::ELECTRIC_CAR          => 'Voiture électrique',
        self::BUS                   => 'Bus',
        self::ON_FOOT               => 'À pied',
        self::ELECTRIC_BIKE         => 'Vélo électrique',
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
            case self::AVION:
                return '✈️';
            case self::TGV:
                return '🚄';
            case self::INTERCITES:
                return '🚆';
            case self::CAR:
                return '🚗';
            case self::ELECTRIC_CAR:
                return '🚗⚡';
            case self::BUS:
                return '🚌';
            case self::ON_FOOT:
                return '🚶';
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
            self::AVION,
            self::TGV,
            self::INTERCITES,
            self::CAR,
            self::ELECTRIC_CAR,
            self::BUS,
            self::ON_FOOT,
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
        ];
    }
}
