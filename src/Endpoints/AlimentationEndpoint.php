<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\AlimentationCategoryEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class AlimentationEndpoint extends Endpoint
{
    public const ENDPOINT = 'alimentation';

    /**
     * AlimentationEndpoint constructor.
     *
     * @param string $category The category type: "group", "rayon", or "popularity"
     *                         - group: food groups (meats, fish, dairy products...)
     *                         - rayon: store aisles (savory grocery, bakery, deli...)
     *                         - popularity: the 10 most consumed foods
     */
    public function __construct(string $category)
    {
        if (!in_array($category, AlimentationCategoryEnum::toArray(), true)) {
            throw new InvalidArgumentException('Invalid category. Must be one of: group, rayon, popularity');
        }

        parent::__construct(self::ENDPOINT, [], ['category' => $category, 'language' => null]);
    }
}
