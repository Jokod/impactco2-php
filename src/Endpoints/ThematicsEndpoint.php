<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\ApiResponse;
use Jokod\Impactco2Php\Entity\Thematic;

class ThematicsEndpoint extends Endpoint
{
    public const ENDPOINT = 'thematiques';

    public function __construct()
    {
        parent::__construct(self::ENDPOINT);
    }

    /**
     * @param array<string, mixed> $raw
     * @return ApiResponse
     */
    public function transformResponse(array $raw): ApiResponse
    {
        $data = $raw['data'] ?? [];
        $items = \is_array($data)
            ? array_map(fn (array $item): Thematic => Thematic::fromArray($item), $data)
            : [];

        return new ApiResponse(
            $items,
            isset($raw['warning']) && \is_string($raw['warning']) ? $raw['warning'] : null
        );
    }
}
