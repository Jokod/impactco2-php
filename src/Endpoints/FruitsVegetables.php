<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\FoodEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

class FruitsVegetables extends Endpoint
{
    public const ENDPOINT = 'fruitsetlegumes';

    /**
     * FruitsVegetables constructor.
     *
     * @param int|null $month The month of the year (1 to 12, default: current month)
     * @param int[]|null $categories The category of the fruits and vegetables
     */
    public function __construct(?int $month = null, ?array $categories = null)
    {
        if (!is_null($month) && ($month < 1 || $month > 12)) {
            throw new InvalidArgumentException('Month must be between 1 and 12');
        }

        if (!is_null($categories)) {
            if (empty($categories)) {
                throw new InvalidArgumentException('Fruits and vegetables category list cannot be empty');
            }

            $invalidCategories = array_filter($categories, fn($category) => !in_array($category, FoodEnum::toArray(), true));
            if (!empty($invalidCategories)) {
                throw new InvalidArgumentException('Invalid category of fruits and vegetables: ' . implode(', ', $invalidCategories));
            }

            $categories = implode(',', $categories);
        }

        parent::__construct(self::ENDPOINT, [], ['month' => $month, 'category' => $categories, 'language' => null]);
    }
}
