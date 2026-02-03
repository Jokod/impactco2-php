<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $item = new Item(123, 456.78);
        
        $this->assertSame(123, $item->getId());
        $this->assertSame(456.78, $item->getValue());
    }

    public function testConstructorThrowsExceptionForInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item ID must be a positive integer');
        
        new Item(0, 456.78);
    }

    public function testConstructorThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item ID must be a positive integer');
        
        new Item(-1, 456.78);
    }

    public function testConstructorThrowsExceptionForNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item value cannot be negative');
        
        new Item(123, -10.0);
    }

    public function testFromArrayCreatesValidItem(): void
    {
        $data = [
            'id'    => 5,
            'value' => 100.5,
        ];

        $item = Item::fromArray($data);

        $this->assertSame(5, $item->getId());
        $this->assertSame(100.5, $item->getValue());
    }

    public function testFromArrayWithNullIdAcceptsApiFootprintDetail(): void
    {
        $data = [
            'id'    => null,
            'value' => 12.3,
        ];

        $item = Item::fromArray($data);

        $this->assertSame(1, $item->getId());
        $this->assertSame(12.3, $item->getValue());
    }

    public function testFromArrayWithEmptyStringIdUsesDefaultId(): void
    {
        $data = [
            'id'    => '',
            'value' => 5.0,
        ];

        $item = Item::fromArray($data);

        $this->assertSame(1, $item->getId());
        $this->assertSame(5.0, $item->getValue());
    }

    public function testFromArrayWithNegativeIdNormalizesToDefaultId(): void
    {
        $data = [
            'id'    => -10,
            'value' => 3.14,
        ];

        $item = Item::fromArray($data);

        $this->assertSame(1, $item->getId());
        $this->assertSame(3.14, $item->getValue());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $item = new Item(123, 456.78);
        $array = $item->toArray();
        
        $this->assertSame([
            'id'    => 123,
            'value' => 456.78,
        ], $array);
    }

    public function testItemIsImmutable(): void
    {
        $item = new Item(123, 456.78);
        
        $reflection = new \ReflectionClass($item);
        $this->assertTrue($reflection->isReadOnly());
    }
}
