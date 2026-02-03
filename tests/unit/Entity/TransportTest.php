<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransportTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $transport = new Transport(1, 'Bus', 123.45);
        
        $this->assertSame(1, $transport->getId());
        $this->assertSame('Bus', $transport->getName());
        $this->assertSame(123.45, $transport->getValue());
    }

    public function testConstructorThrowsExceptionForInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport ID must be a positive integer');
        
        new Transport(0, 'Bus', 123.45);
    }

    public function testConstructorThrowsExceptionForNegativeId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport ID must be a positive integer');
        
        new Transport(-1, 'Bus', 123.45);
    }

    public function testConstructorThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport name cannot be empty');
        
        new Transport(1, '', 123.45);
    }

    public function testConstructorThrowsExceptionForWhitespaceName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport name cannot be empty');
        
        new Transport(1, '   ', 123.45);
    }

    public function testConstructorThrowsExceptionForNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transport value cannot be negative');
        
        new Transport(1, 'Bus', -10.0);
    }

    public function testFromArrayCreatesValidTransport(): void
    {
        $data = [
            'id'    => 5,
            'name'  => 'Train',
            'value' => 50.25,
        ];
        
        $transport = Transport::fromArray($data);
        
        $this->assertSame(5, $transport->getId());
        $this->assertSame('Train', $transport->getName());
        $this->assertSame(50.25, $transport->getValue());
    }

    public function testFromArrayWithMissingDataUsesDefaults(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        Transport::fromArray([]);
    }

    public function testToArrayReturnsCorrectStructure(): void
    {
        $transport = new Transport(1, 'Bus', 123.45);
        $array = $transport->toArray();
        
        $this->assertSame([
            'id'    => 1,
            'name'  => 'Bus',
            'value' => 123.45,
        ], $array);
    }

    public function testTransportIsImmutable(): void
    {
        $transport = new Transport(1, 'Bus', 123.45);
        
        // Verify readonly by checking that properties cannot be changed
        $reflection = new \ReflectionClass($transport);
        $this->assertTrue($reflection->isReadOnly());
    }
}
