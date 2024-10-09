<?php

namespace Jokod\Impactco2Php\Enum;

class ThematicEnum
{
    public const NUMERIC = 1;
    public const MEAL = 2;
    public const DRINK = 3;
    public const TRANSPORT = 4;
    public const CLOTHING = 5;
    public const APPLIANCE = 6;
    public const FURNITURE = 7;
    public const HEATING = 8;
    public const FRUITS_AND_VEGETABLES = 9;
    public const DIGITAL_USAGE = 10;
    public const CASE_STUDIES = 13;

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::NUMERIC               => 'Numeric',
        self::MEAL                  => 'Meal',
        self::DRINK                 => 'Drink',
        self::TRANSPORT             => 'Transport',
        self::CLOTHING              => 'Clothing',
        self::APPLIANCE             => 'Appliance',
        self::FURNITURE             => 'Furniture',
        self::HEATING               => 'Heating',
        self::FRUITS_AND_VEGETABLES => 'Fruits and Vegetables',
        self::DIGITAL_USAGE         => 'Digital Usage',
        self::CASE_STUDIES          => 'Case Studies',
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
            case self::NUMERIC:
                return '💻';
            case self::MEAL:
                return '🍽️';
            case self::DRINK:
                return '🍹';
            case self::TRANSPORT:
                return '🚗';
            case self::CLOTHING:
                return '👗';
            case self::APPLIANCE:
                return '🔌';
            case self::FURNITURE:
                return '🛋️';
            case self::HEATING:
                return '🔥';
            case self::FRUITS_AND_VEGETABLES:
                return '🍎';
            case self::DIGITAL_USAGE:
                return '📱';
            case self::CASE_STUDIES:
                return '📚';
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
            self::NUMERIC,
            self::MEAL,
            self::DRINK,
            self::TRANSPORT,
            self::CLOTHING,
            self::APPLIANCE,
            self::FURNITURE,
            self::HEATING,
            self::FRUITS_AND_VEGETABLES,
            self::DIGITAL_USAGE,
            self::CASE_STUDIES,
        ];
    }
}
