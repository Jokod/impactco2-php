<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ThematicTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $thematic = new Thematic(1, 'Climate Change', 'climate-change');
        
        $this->assertSame(1, $thematic->getId());
        $this->assertSame('Climate Change', $thematic->getName());
        $this->assertSame('climate-change', $thematic->getSlug());
    }

    public function testConstructorThrowsExceptionForInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic ID must be a positive integer');
        
        new Thematic(0, 'Climate Change', 'climate-change');
    }

    public function testConstructorThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic ID must be a positive integer');
        
        new Thematic(-1, 'Climate Change', 'climate-change');
    }

    public function testConstructorThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic name cannot be empty');
        
        new Thematic(1, '', 'climate-change');
    }

    public function testConstructorThrowsExceptionForWhitespaceName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic name cannot be empty');
        
        new Thematic(1, '   ', 'climate-change');
    }

    public function testConstructorThrowsExceptionForEmptySlug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic slug cannot be empty');
        
        new Thematic(1, 'Climate Change', '');
    }

    public function testConstructorThrowsExceptionForWhitespaceSlug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Thematic slug cannot be empty');
        
        new Thematic(1, 'Climate Change', '   ');
    }

    public function testFromArrayCreatesValidThematic(): void
    {
        $data = [
            'id'   => 5,
            'name' => 'Energy',
            'slug' => 'energy',
        ];
        
        $thematic = Thematic::fromArray($data);
        
        $this->assertSame(5, $thematic->getId());
        $this->assertSame('Energy', $thematic->getName());
        $this->assertSame('energy', $thematic->getSlug());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $thematic = new Thematic(1, 'Climate Change', 'climate-change');
        $array = $thematic->toArray();
        
        $this->assertSame([
            'id'   => 1,
            'name' => 'Climate Change',
            'slug' => 'climate-change',
        ], $array);
    }

    public function testThematicIsImmutable(): void
    {
        $thematic = new Thematic(1, 'Climate Change', 'climate-change');
        
        $reflection = new \ReflectionClass($thematic);
        $this->assertTrue($reflection->isReadOnly());
    }
}
