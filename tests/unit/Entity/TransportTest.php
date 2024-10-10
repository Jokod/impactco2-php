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
        $this->assertEquals(1, $transport->getId());
    }

    public function testGetSetName(): void
    {
        $transport = new Transport();
        $transport->setName('Bus');
        $this->assertEquals('Bus', $transport->getName());
    }

    public function testGetSetValue(): void
    {
        $transport = new Transport();
        $transport->setValue(123.45);
        $this->assertEquals(123.45, $transport->getValue());
    }
}
