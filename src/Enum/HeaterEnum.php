<?php

namespace Jokod\Impactco2Php\Enum;

class HeaterEnum extends AbstractEnum
{
    public const GAS_HEATING = 1;
    public const FUEL_OIL_HEATING = 2;
    public const ELECTRIC_HEATING = 3;
    public const HEAT_PUMP_HEATING = 4;
    public const PELLET_STOVE_HEATING = 5;
    public const WOOD_STOVE_HEATING = 6;
    public const DISTRICT_HEATING = 7;

    protected static array $names = [
        self::GAS_HEATING          => 'Chauffage au gaz',
        self::FUEL_OIL_HEATING     => 'Chauffage au fioul',
        self::ELECTRIC_HEATING     => 'Chauffage électrique',
        self::HEAT_PUMP_HEATING    => 'Chauffage avec une pompe à chaleur',
        self::PELLET_STOVE_HEATING => 'Chauffage avec un poêle à granulés',
        self::WOOD_STOVE_HEATING   => 'Chauffage avec un poêle à bois',
        self::DISTRICT_HEATING     => 'Chauffage via un réseau de chaleur',
    ];

    protected static array $emojis = [
        self::GAS_HEATING => '🔥',
        self::FUEL_OIL_HEATING => '🛢️',
        self::ELECTRIC_HEATING => '⚡',
        self::HEAT_PUMP_HEATING => '🌡️',
        self::PELLET_STOVE_HEATING => '🌾',
        self::WOOD_STOVE_HEATING => '🌲',
        self::DISTRICT_HEATING => '🏢',
    ];

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
        ];
    }
}
