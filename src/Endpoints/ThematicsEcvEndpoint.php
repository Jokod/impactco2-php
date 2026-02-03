<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\ApiResponse;
use Jokod\Impactco2Php\Entity\ECV;
use Jokod\Impactco2Php\Enum\ThematicEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class ThematicsEcvEndpoint extends Endpoint
{
    public const ENDPOINT = 'thematiques/ecv';

    /**
     * @param int|string $id ID (ThematicEnum) ou slug de la thématique (ex: "mobilier", "transport")
     * @param int|null $detail 1 = détail du calcul ECV, 0 = total uniquement (défaut: 0)
     */
    public function __construct(int|string $id, ?int $detail = 0)
    {
        $resolvedId = \is_string($id) ? ThematicEnum::getIdFromSlug($id) : $id;
        if ($resolvedId === null || !\in_array($resolvedId, ThematicEnum::toArray(), true)) {
            throw new InvalidArgumentException('Invalid thematic ECV identifier or slug');
        }

        if (!\in_array($detail, [0, 1], true)) {
            throw new InvalidArgumentException('Detail must be 0 or 1');
        }

        $pathId = \is_string($id) ? $id : $resolvedId;
        parent::__construct(self::ENDPOINT, [$pathId], ['detail' => $detail, 'language' => null]);
    }

    /**
     * @param array<string, mixed> $raw
     * @return ApiResponse
     */
    public function transformResponse(array $raw): ApiResponse
    {
        $data = $raw['data'] ?? [];
        $ecv = \is_array($data) && $data !== [] ? ECV::fromArray($data) : $data;

        return new ApiResponse(
            $ecv,
            isset($raw['warning']) && \is_string($raw['warning']) ? $raw['warning'] : null
        );
    }
}
