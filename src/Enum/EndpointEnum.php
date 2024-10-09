<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

class EndpointEnum
{
    public const THEMATICS = 'thematiques';
    public const THEMATICS_ECV = 'thematiques/ecv';
    public const HEATER = 'chauffage';
    public const FRUITS_VEGETABLES = 'fruitsetlegumes';
    public const TRANSPORT = 'transport';

    /**
     * @return string[] array of endpoints
     */
    public static function toArray(): array
    {
        return [
            self::THEMATICS,
            self::THEMATICS_ECV,
            self::HEATER,
            self::FRUITS_VEGETABLES,
            self::TRANSPORT,
        ];
    }
}
