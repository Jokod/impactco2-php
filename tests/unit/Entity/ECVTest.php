<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use PHPUnit\Framework\TestCase;

class ECVTest extends TestCase
{
    public function testGetName(): void
    {
        $ecv = new ECV();
        $ecv->setName('Test Name');
        $this->assertSame('Test Name', $ecv->getName());
    }

    public function testSetName(): void
    {
        $ecv = new ECV();
        $ecv->setName('Test Name');
        $this->assertSame('Test Name', $ecv->getName());
    }

    public function testGetEcv(): void
    {
        $ecv = new ECV();
        $ecv->setEcv(123.45);
        $this->assertSame(123.45, $ecv->getEcv());
    }

    public function testSetEcv(): void
    {
        $ecv = new ECV();
        $ecv->setEcv(123.45);
        $this->assertSame(123.45, $ecv->getEcv());
    }

    public function testGetSlug(): void
    {
        $ecv = new ECV();
        $ecv->setSlug('test-slug');
        $this->assertSame('test-slug', $ecv->getSlug());
    }

    public function testSetSlug(): void
    {
        $ecv = new ECV();
        $ecv->setSlug('test-slug');
        $this->assertSame('test-slug', $ecv->getSlug());
    }

    public function testGetFootprint(): void
    {
        $ecv = new ECV();
        $ecv->setFootprint(456.78);
        $this->assertSame(456.78, $ecv->getFootprint());
    }

    public function testSetFootprint(): void
    {
        $ecv = new ECV();
        $ecv->setFootprint(456.78);
        $this->assertSame(456.78, $ecv->getFootprint());
    }

    public function testGetItems(): void
    {
        $items = [new Item(), new Item()];
        $ecv = new ECV();
        $ecv->setItems($items);
        $this->assertSame($items, $ecv->getItems());
    }

    public function testSetItems(): void
    {
        $items = [new Item(), new Item()];
        $ecv = new ECV();
        $ecv->setItems($items);
        $this->assertSame($items, $ecv->getItems());
    }

    public function testGetUsage(): void
    {
        $usage = new Usage();
        $ecv = new ECV();
        $ecv->setUsage($usage);
        $this->assertSame($usage, $ecv->getUsage());
    }

    public function testSetUsage(): void
    {
        $usage = new Usage();
        $ecv = new ECV();
        $ecv->setUsage($usage);
        $this->assertSame($usage, $ecv->getUsage());
    }

    public function testGetEndOfLife(): void
    {
        $ecv = new ECV();
        $ecv->setEndOfLife(789.01);
        $this->assertSame(789.01, $ecv->getEndOfLife());
    }

    public function testSetEndOfLife(): void
    {
        $ecv = new ECV();
        $ecv->setEndOfLife(789.01);
        $this->assertSame(789.01, $ecv->getEndOfLife());
    }
}
