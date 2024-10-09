<?php

namespace Jokod\Impactco2Php\Enum;

class FoodEnum
{
    public const FRUITS = 1;
    public const VEGETABLES = 2;
    public const HERBS = 3;
    public const PASTA_RICE_CEREALS = 4;
    public const POTATOES_TUBERS = 5;
    public const NUTS_SEEDS = 6;

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::FRUITS             => 'fruits',
        self::VEGETABLES         => 'légumes',
        self::HERBS              => 'herbes',
        self::PASTA_RICE_CEREALS => 'pâtes, riz et céréales',
        self::POTATOES_TUBERS    => 'pommes de terre et autres tubercules',
        self::NUTS_SEEDS         => 'fruits à coque et graines oléagineuses',
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
            case self::FRUITS:
                return '🍎';
            case self::VEGETABLES:
                return '🥦';
            case self::HERBS:
                return '🌿';
            case self::PASTA_RICE_CEREALS:
                return '🍚';
            case self::POTATOES_TUBERS:
                return '🥔';
            case self::NUTS_SEEDS:
                return '🌰';
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
            self::FRUITS,
            self::VEGETABLES,
            self::HERBS,
            self::PASTA_RICE_CEREALS,
            self::POTATOES_TUBERS,
            self::NUTS_SEEDS,
        ];
    }
}
