<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Endpoints;

use Jokod\Impactco2Php\Endpoints\FruitsVegetables;
use Jokod\Impactco2Php\Enum\FoodEnum;
use PHPUnit\Framework\TestCase;

class FruitsVegetablesTest extends TestCase
{
    public function testConstructorWithInvalidMonth(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Month must be between 1 and 12');
        new FruitsVegetables(13);
    }

    public function testConstructorWithEmptyCategories(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Fruits and vegetables category list cannot be empty');
        new FruitsVegetables(1, []);
    }

    public function testConstructorWithInvalidCategory(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid category of fruits and vegetables: 999');
        new FruitsVegetables(1, [999]);
    }

    public function testConstructorWithValidInputs(): void
    {
        $month = 5;
        $categories = [FoodEnum::FRUITS, FoodEnum::VEGETABLES];
        $endpoint = new FruitsVegetables($month, $categories);

        $this->assertInstanceOf(FruitsVegetables::class, $endpoint);
        $this->assertEquals('fruitsetlegumes', $endpoint::ENDPOINT);
    }

    public function testConstructorWithNullInputs(): void
    {
        $endpoint = new FruitsVegetables();

        $this->assertInstanceOf(FruitsVegetables::class, $endpoint);
        $this->assertEquals('fruitsetlegumes', $endpoint::ENDPOINT);
    }
}
