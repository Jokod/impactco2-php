<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

class HeaterEnum
{
    public const GAS_HEATING = 1;
    public const FUEL_OIL_HEATING = 2;
    public const ELECTRIC_HEATING = 3;
    public const HEAT_PUMP_HEATING = 4;
    public const PELLET_STOVE_HEATING = 5;
    public const WOOD_STOVE_HEATING = 6;
    public const DISTRICT_HEATING = 7;
    public const PELLET_BOILER_HEATING = 8;

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::GAS_HEATING          => 'Chauffage au gaz',
        self::FUEL_OIL_HEATING     => 'Chauffage au fioul',
        self::ELECTRIC_HEATING     => 'Chauffage électrique',
        self::HEAT_PUMP_HEATING    => 'Chauffage avec une pompe à chaleur',
        self::PELLET_STOVE_HEATING => 'Chauffage avec un poêle à granulés',
        self::WOOD_STOVE_HEATING   => 'Chauffage avec un poêle à bois',
        self::DISTRICT_HEATING     => 'Chauffage via un réseau de chaleur',
        self::PELLET_BOILER_HEATING => 'Chauffage avec une chaudière à granulés',
    ];

    public static function getName(?int $id): string
    {
        if (!isset(self::$names[$id])) {
            return 'Undefined';
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
            case self::GAS_HEATING:
                return '🔥';
            case self::FUEL_OIL_HEATING:
                return '🛢️';
            case self::ELECTRIC_HEATING:
                return '⚡';
            case self::HEAT_PUMP_HEATING:
                return '🌡️';
            case self::PELLET_STOVE_HEATING:
                return '🌾';
            case self::WOOD_STOVE_HEATING:
                return '🌲';
            case self::DISTRICT_HEATING:
                return '🏢';
            case self::PELLET_BOILER_HEATING:
                return '♨️';
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
            self::GAS_HEATING,
            self::FUEL_OIL_HEATING,
            self::ELECTRIC_HEATING,
            self::HEAT_PUMP_HEATING,
            self::PELLET_STOVE_HEATING,
            self::WOOD_STOVE_HEATING,
            self::DISTRICT_HEATING,
            self::PELLET_BOILER_HEATING,
        ];
    }
}
