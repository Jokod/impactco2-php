<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\FoodEnum;

class FruitsVegetables extends Endpoint
{
    public const ENDPOINT = 'fruitsetlegumes';

    /**
     * FruitsVegetables constructor.
     *
     * @param int|null $month The month of the year (1 to 12, default: current month)
     * @param int[] $categories The category of the fruits and vegetables
     */
    public function __construct(int $month = null, array $categories = null)
    {
        if (!is_null($month) && ($month < 1 || $month > 12)) {
            throw new \InvalidArgumentException('Month must be between 1 and 12');
        }

        if (!is_null($categories)) {
            if (empty($categories)) {
                throw new \InvalidArgumentException('Fruits and vegetables category list cannot be empty');
            }

            foreach ($categories as $category) {
                if (!in_array($category, FoodEnum::toArray())) {
                    throw new \InvalidArgumentException('Invalid category of fruits and vegetables: ' . $category);
                }
            }

            $categories = implode(',', $categories);
        }

        parent::__construct(self::ENDPOINT, [], ['month' => $month, 'category' => $categories, 'language' => null]);
    }
}
