<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use PHPUnit\Framework\TestCase;

class TransportTest extends TestCase
{
    public function testGetSetId(): void
    {
        $transport = new Transport();
        $transport->setId(1);
        $this->assertSame(1, $transport->getId());
    }

    public function testGetSetName(): void
    {
        $transport = new Transport();
        $transport->setName('Bus');
        $this->assertSame('Bus', $transport->getName());
    }

    public function testGetSetValue(): void
    {
        $transport = new Transport();
        $transport->setValue(123.45);
        $this->assertSame(123.45, $transport->getValue());
    }
}
