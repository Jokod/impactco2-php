<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Entity;

use Jokod\Impactco2Php\Entity\Usage;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UsageTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $usage = new Usage(100.5, 5);
        
        $this->assertSame(100.5, $usage->getPerYear());
        $this->assertSame(5, $usage->getDefaultYears());
    }

    public function testConstructorThrowsExceptionForNegativePerYear(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Usage per year cannot be negative');
        
        new Usage(-10.0, 5);
    }

    public function testConstructorThrowsExceptionForInvalidDefaultYears(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Default years must be a positive integer');
        
        new Usage(100.5, 0);
    }

    public function testConstructorThrowsExceptionForNegativeDefaultYears(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Default years must be a positive integer');
        
        new Usage(100.5, -5);
    }

    public function testFromArrayCreatesValidUsage(): void
    {
        $data = [
            'perYear'      => 200.75,
            'defaultYears' => 3,
        ];

        $usage = Usage::fromArray($data);

        $this->assertSame(200.75, $usage->getPerYear());
        $this->assertSame(3, $usage->getDefaultYears());
    }

    public function testFromArrayAcceptsApiLowercaseKeys(): void
    {
        $data = [
            'peryear'      => 25.3,
            'defaultyears' => 5,
        ];

        $usage = Usage::fromArray($data);

        $this->assertSame(25.3, $usage->getPerYear());
        $this->assertSame(5, $usage->getDefaultYears());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $usage = new Usage(100.5, 5);
        $array = $usage->toArray();
        
        $this->assertSame([
            'perYear'      => 100.5,
            'defaultYears' => 5,
        ], $array);
    }

    public function testUsageIsImmutable(): void
    {
        $usage = new Usage(100.5, 5);
        
        $reflection = new \ReflectionClass($usage);
        $this->assertTrue($reflection->isReadOnly());
    }
}
