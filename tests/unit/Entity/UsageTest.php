<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Entity;

use Jokod\Impactco2Php\Entity\Usage;
use PHPUnit\Framework\TestCase;

class UsageTest extends TestCase
{
    public function testSetAndGetPerYear(): void
    {
        $usage = new Usage();
        $perYear = 100.5;
        $usage->setPerYear($perYear);
        $this->assertEquals($perYear, $usage->getPerYear());
    }

    public function testSetAndGetDefaultYears(): void
    {
        $usage = new Usage();
        $defaultYears = 5;
        $usage->setDefaultYears($defaultYears);
        $this->assertEquals($defaultYears, $usage->getDefaultYears());
    }
}
