<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

class LanguagesEnum
{
    public const FR = 'fr';
    public const EN = 'en';
    public const ES = 'es';
    public const DE = 'de';

    /**
     * @return string default language
     */
    public static function default(): string
    {
        return self::FR;
    }

    /**
     * @return string[] array of languages
     */
    public static function toArray(): array
    {
        return [
            self::FR,
            self::EN,
            self::ES,
            self::DE,
        ];
    }
}
