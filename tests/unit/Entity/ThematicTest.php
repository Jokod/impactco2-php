<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use PHPUnit\Framework\TestCase;

class ThematicTest extends TestCase
{
    public function testGetAndSetId(): void
    {
        $thematic = new Thematic();
        $thematic->setId(1);
        $this->assertEquals(1, $thematic->getId());
    }

    public function testGetAndSetName(): void
    {
        $thematic = new Thematic();
        $thematic->setName('Climate Change');
        $this->assertEquals('Climate Change', $thematic->getName());
    }

    public function testGetAndSetSlug(): void
    {
        $thematic = new Thematic();
        $thematic->setSlug('climate-change');
        $this->assertEquals('climate-change', $thematic->getSlug());
    }
}
