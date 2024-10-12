<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\ThematicEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class ThematicsEcvEndpoint extends Endpoint
{
    public const ENDPOINT = 'thematiques/ecv';

    /**
     * ThematicsEcvEndpoint constructor.
     *
     * @param int $id The thematic ECV identifier
     * @param int|null $detail return the detail of the calculation of the ecv. Otherwise return only the total (0 or 1, default: 0)
     */
    public function __construct(int $id, ?int $detail = 0)
    {
        if (!in_array($id, ThematicEnum::toArray(), true)) {
            throw new InvalidArgumentException('Invalid thematic ECV identifier');
        }

        if (!in_array($detail, [0, 1], true)) {
            throw new InvalidArgumentException('Detail must be 0 or 1');
        }

        parent::__construct(self::ENDPOINT, ['id' => $id], ['detail' => $detail, 'language' => null]);
    }
}
