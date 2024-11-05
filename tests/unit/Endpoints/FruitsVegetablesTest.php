<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\FruitsVegetables;
use Jokod\Impactco2Php\Enum\FoodEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FruitsVegetablesTest extends TestCase
{
    public function testConstructorWithInvalidMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Month must be between 1 and 12');
        new FruitsVegetables(13);
    }

    public function testConstructorWithZeroMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Month must be between 1 and 12');
        new FruitsVegetables(0);
    }

    public function testConstructorWithNegativeMonth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Month must be between 1 and 12');
        new FruitsVegetables(-1);
    }

    public function testConstructorWithEmptyCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Fruits and vegetables category list cannot be empty');
        new FruitsVegetables(1, []);
    }

    public function testConstructorWithSingleInvalidCategory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category of fruits and vegetables: 999');
        new FruitsVegetables(1, [999]);
    }

    public function testConstructorWithMultipleInvalidCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category of fruits and vegetables: 999, 998');
        new FruitsVegetables(1, [999, 998]);
    }

    public function testConstructorWithValidInputs(): void
    {
        $month = 5;
        $categories = [FoodEnum::FRUITS, FoodEnum::VEGETABLES];
        $endpoint = new FruitsVegetables($month, $categories);

        $this->assertInstanceOf(FruitsVegetables::class, $endpoint);
    }

    public function testGetPathWithAllParameters(): void
    {
        $endpoint = new FruitsVegetables(5, [FoodEnum::FRUITS, FoodEnum::VEGETABLES]);
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('fruitsetlegumes', $path);
        $this->assertStringContainsString('month=5', $path);
        $this->assertStringContainsString('category=' . FoodEnum::FRUITS . ',' . FoodEnum::VEGETABLES, $path);
        $this->assertStringContainsString('language=fr', $path);
    }

    public function testGetPathWithoutOptionalParameters(): void
    {
        $endpoint = new FruitsVegetables();
        $path = $endpoint->getPath('fr');

        $this->assertStringContainsString('fruitsetlegumes', $path);
        $this->assertStringNotContainsString('month=', $path);
        $this->assertStringNotContainsString('category=', $path);
        $this->assertStringContainsString('language=fr', $path);
    }
}
