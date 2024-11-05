<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\TransportsEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class TransportEndpoint extends Endpoint
{
    public const ENDPOINT = 'transport';

    /**
     * TransportEndpoint constructor.
     *
     * @param int $distance The distance in kilometers (km)
     *                      Example: below 500km the data of the plane will not be visible.
     * @param int[] $transports List of ids of the transports
     * @param bool|null $displayAll Return the emission calculation for all available transports. Otherwise return only those that make sense for the given distance
     * @param int|null $ignoreRadiativeForcing Ignore the radiative forcing of the plane
     * @param int|null $occupencyRate The occupancy rate of the vehicle
     * @param int|null $includeConstruction Include the construction of the vehicle
     */
    public function __construct(
        int $distance,
        ?array $transports = null,
        ?bool $displayAll = false,
        ?int $occupencyRate = 1,
        ?int $includeConstruction = 0,
        ?int $ignoreRadiativeForcing = 0,
    ) {
        if ($distance <= 0) {
            throw new InvalidArgumentException('Distance must be a positive integer');
        }

        if (!is_null($transports)) {
            if (empty($transports)) {
                throw new InvalidArgumentException('Transport list cannot be empty');
            }

            // Vérifier tous les transports invalides et les collecter
            $invalidTransports = array_filter($transports, fn($transport) => !in_array($transport, TransportsEnum::toArray(), true));
            if (!empty($invalidTransports)) {
                throw new InvalidArgumentException('Invalid transport identifier: ' . implode(', ', $invalidTransports));
            }

            $transports = implode(',', $transports);
        }

        if ($occupencyRate < 0) {
            throw new InvalidArgumentException('Occupency rate must be a positive integer');
        }

        if (!in_array($ignoreRadiativeForcing, [0, 1], true)) {
            throw new InvalidArgumentException('Ignore radiative forcing must be 0 or 1');
        }

        if (!in_array($includeConstruction, [0, 1], true)) {
            throw new InvalidArgumentException('Include construction must be 0 or 1');
        }

        parent::__construct(self::ENDPOINT, [], [
            'km'                     => $distance,
            'displayAll'             => (int) $displayAll,
            'transports'             => $transports,
            'ignoreRadiativeForcing' => $ignoreRadiativeForcing,
            'occupencyRate'          => $occupencyRate,
            'includeConstruction'    => $includeConstruction,
            'language'               => null,
        ]);
    }
}
