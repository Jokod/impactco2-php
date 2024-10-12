<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\HeaterEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class HeaterEndpoint extends Endpoint
{
    public const ENDPOINT = 'chauffage';

    /**
     * HeaterEnpoint constructor.
     *
     * @param int|null $surface Number of square meters on which to calculate emissions. If not specified, use the average size of an apartment in France: 63m2
     * @param int[]|null $types List of the type of heating
     */
    public function __construct(?int $surface = null, ?array $types = null)
    {
        if (!is_null($surface) && $surface < 0) {
            throw new InvalidArgumentException('Surface must be a positive integer');
        }

        if (!is_null($types)) {
            if (empty($types)) {
                throw new InvalidArgumentException('Heating type list cannot be empty');
            }

            foreach ($types as $type) {
                if (!in_array($type, HeaterEnum::toArray(), true)) {
                    throw new InvalidArgumentException('Invalid type of heating: ' . $type);
                }
            }

            $types = implode(',', $types);
        }

        parent::__construct(self::ENDPOINT, [], ['m2' => $surface, 'chauffages' => $types, 'language' => null]);
    }
}
