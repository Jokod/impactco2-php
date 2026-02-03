<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\ApiResponse;
use Jokod\Impactco2Php\Entity\Transport;
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

            foreach ($transports as $transport) {
                if (!in_array($transport, TransportsEnum::toArray(), true)) {
                    throw new InvalidArgumentException('Invalid transport identifier: ' . $transport);
                }
            }

            $transports = implode(',', $transports);
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

    /**
     * @param array<string, mixed> $raw
     * @return ApiResponse
     */
    public function transformResponse(array $raw): ApiResponse
    {
        $data = $raw['data'] ?? [];
        $items = \is_array($data)
            ? array_map(fn (array $item): Transport => Transport::fromArray($item), $data)
            : [];

        return new ApiResponse(
            $items,
            isset($raw['warning']) && \is_string($raw['warning']) ? $raw['warning'] : null
        );
    }
}
