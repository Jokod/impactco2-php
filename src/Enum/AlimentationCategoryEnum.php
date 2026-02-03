<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

class AlimentationCategoryEnum
{
    public const GROUP = 'group';
    public const RAYON = 'rayon';
    public const POPULARITY = 'popularity';

    /**
     * List of names for each type
     *
     * @var string[] array
     */
    public static $names = [
        self::GROUP      => 'Groupes d\'aliments',
        self::RAYON      => 'Rayons du magasin',
        self::POPULARITY => 'Aliments les plus consommés',
    ];

    public static function getName(?string $id): string
    {
        if (!isset(self::$names[$id])) {
            return 'Undefined';
        }

        return self::$names[$id];
    }

    /**
     * @return string[] array of types
     */
    public static function toArray(): array
    {
        return [
            self::GROUP,
            self::RAYON,
            self::POPULARITY,
        ];
    }
}
