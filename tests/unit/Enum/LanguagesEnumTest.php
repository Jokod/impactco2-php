<?php

declare(strict_types = 1);

use Jokod\Impactco2Php\Enum\LanguagesEnum;
use PHPUnit\Framework\TestCase;

class LanguagesEnumTest extends TestCase
{
    public function testDefault(): void
    {
        $this->assertSame('fr', LanguagesEnum::default());
    }

    public function testToArray(): void
    {
        $expected = ['fr', 'en', 'es', 'de'];
        $this->assertSame($expected, LanguagesEnum::toArray());
    }
}
