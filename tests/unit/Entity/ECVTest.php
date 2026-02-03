<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ECVTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $items = $this->createValidItems();
        $usage = $this->createValidUsage();
        
        $ecv = new ECV(
            'Test Name',
            123.45,
            'test-slug',
            456.78,
            $items,
            $usage,
            789.01
        );
        
        $this->assertSame('Test Name', $ecv->getName());
        $this->assertSame(123.45, $ecv->getEcv());
        $this->assertSame('test-slug', $ecv->getSlug());
        $this->assertSame(456.78, $ecv->getFootprint());
        $this->assertSame($items, $ecv->getItems());
        $this->assertSame($usage, $ecv->getUsage());
        $this->assertSame(789.01, $ecv->getEndOfLife());
    }

    public function testConstructorThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ECV name cannot be empty');
        
        new ECV(
            '',
            123.45,
            'test-slug',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForWhitespaceName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ECV name cannot be empty');
        
        new ECV(
            '   ',
            123.45,
            'test-slug',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForNegativeEcv(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ECV value cannot be negative');
        
        new ECV(
            'Test Name',
            -10.0,
            'test-slug',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForEmptySlug(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ECV slug cannot be empty');
        
        new ECV(
            'Test Name',
            123.45,
            '',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForNegativeFootprint(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Footprint cannot be negative');
        
        new ECV(
            'Test Name',
            123.45,
            'test-slug',
            -10.0,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForInvalidItems(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('All items must be instances of Item');
        
        new ECV(
            'Test Name',
            123.45,
            'test-slug',
            456.78,
            ['not an item'],
            $this->createValidUsage(),
            789.01
        );
    }

    public function testConstructorThrowsExceptionForNegativeEndOfLife(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('End of life value cannot be negative');
        
        new ECV(
            'Test Name',
            123.45,
            'test-slug',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            -10.0
        );
    }

    public function testFromArrayCreatesValidECV(): void
    {
        $data = [
            'name'      => 'Product',
            'ecv'       => 100.0,
            'slug'      => 'product',
            'footprint' => 50.0,
            'items'     => [
                ['id' => 1, 'value' => 10.0],
                ['id' => 2, 'value' => 20.0],
            ],
            'usage' => [
                'perYear'      => 100.0,
                'defaultYears' => 5,
            ],
            'endOfLife' => 30.0,
        ];
        
        $ecv = ECV::fromArray($data);
        
        $this->assertSame('Product', $ecv->getName());
        $this->assertSame(100.0, $ecv->getEcv());
        $this->assertSame('product', $ecv->getSlug());
        $this->assertSame(50.0, $ecv->getFootprint());
        $this->assertCount(2, $ecv->getItems());
        $this->assertSame(100.0, $ecv->getUsage()->getPerYear());
        $this->assertSame(30.0, $ecv->getEndOfLife());
    }

    public function testFromArrayWithFootprintDetailAcceptsApiResponse(): void
    {
        $data = [
            'name'           => 'Eau en bouteille',
            'ecv'            => 50.3,
            'slug'           => 'eauenbouteille',
            'footprint'      => 45.2,
            'footprintDetail' => [
                ['id' => null, 'value' => 12.3],
                ['id' => 2, 'value' => 32.9],
            ],
            'usage' => [
                'peryear'      => 25.3,
                'defaultyears' => 5,
            ],
            'endOfLife' => 45.2,
        ];

        $ecv = ECV::fromArray($data);

        $this->assertSame('Eau en bouteille', $ecv->getName());
        $this->assertSame(50.3, $ecv->getEcv());
        $this->assertSame(45.2, $ecv->getFootprint());
        $this->assertCount(2, $ecv->getItems());
        $this->assertSame(25.3, $ecv->getUsage()->getPerYear());
        $this->assertSame(5, $ecv->getUsage()->getDefaultYears());
        $this->assertSame(45.2, $ecv->getEndOfLife());
    }

    public function testFromArrayWithNonArrayUsageUsesDefaultUsage(): void
    {
        $data = [
            'name'      => 'Product',
            'ecv'       => 10.0,
            'slug'      => 'product',
            'footprint' => 5.0,
            'items'     => [],
            'usage'     => 123,
            'endOfLife' => 1.0,
        ];

        $ecv = ECV::fromArray($data);

        $this->assertSame(0.0, $ecv->getUsage()->getPerYear());
        $this->assertSame(1, $ecv->getUsage()->getDefaultYears());
    }

    public function testFromArraySkipsNonArrayItemsInSource(): void
    {
        $data = [
            'name'      => 'Product',
            'ecv'       => 10.0,
            'slug'      => 'product',
            'footprint' => 5.0,
            'items'     => [
                ['id' => 1, 'value' => 10.0],
                'invalid',
                ['id' => 2, 'value' => 20.0],
            ],
            'usage'     => ['perYear' => 0.0, 'defaultYears' => 1],
            'endOfLife' => 1.0,
        ];

        $ecv = ECV::fromArray($data);

        $this->assertCount(2, $ecv->getItems());
    }

    public function testFromArrayWithNonArrayItemsSourceReturnsEmptyItems(): void
    {
        $data = [
            'name'      => 'Product',
            'ecv'       => 10.0,
            'slug'      => 'product',
            'footprint' => 5.0,
            'items'     => 'not-an-array',
            'usage'     => ['perYear' => 0.0, 'defaultYears' => 1],
            'endOfLife' => 1.0,
        ];

        $ecv = ECV::fromArray($data);

        $this->assertSame([], $ecv->getItems());
        $this->assertSame('Product', $ecv->getName());
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $items = $this->createValidItems();
        $usage = $this->createValidUsage();
        
        $ecv = new ECV(
            'Test Name',
            123.45,
            'test-slug',
            456.78,
            $items,
            $usage,
            789.01
        );
        
        $array = $ecv->toArray();
        
        $this->assertSame('Test Name', $array['name']);
        $this->assertSame(123.45, $array['ecv']);
        $this->assertSame('test-slug', $array['slug']);
        $this->assertSame(456.78, $array['footprint']);
        $this->assertCount(2, $array['items']);
        $this->assertIsArray($array['usage']);
        $this->assertSame(789.01, $array['endOfLife']);
    }

    public function testECVIsImmutable(): void
    {
        $ecv = new ECV(
            'Test Name',
            123.45,
            'test-slug',
            456.78,
            $this->createValidItems(),
            $this->createValidUsage(),
            789.01
        );
        
        $reflection = new \ReflectionClass($ecv);
        $this->assertTrue($reflection->isReadOnly());
    }
    private function createValidUsage(): Usage
    {
        return new Usage(100.0, 5);
    }

    /**
     * @return Item[]
     */
    private function createValidItems(): array
    {
        return [
            new Item(1, 10.5),
            new Item(2, 20.3),
        ];
    }
}
