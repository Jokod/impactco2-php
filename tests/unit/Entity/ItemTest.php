<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testSetAndGetId(): void
    {
        $item = new Item();
        $item->setId(123);
        $this->assertSame(123, $item->getId());
    }

    public function testSetAndGetValue(): void
    {
        $item = new Item();
        $item->setValue(456.78);
        $this->assertSame(456.78, $item->getValue());
    }
}
