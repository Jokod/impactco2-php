<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Enum;

use Jokod\Impactco2Php\Enum\AbstractEnum;
use PHPUnit\Framework\TestCase;

class AbstractEnumTest extends TestCase
{
    public function testGetName(): void
    {
        $enum = new class extends AbstractEnum {
            protected static array $names = [1 => 'Test'];
            public static function toArray(): array
            {
                return [1];
            }
        };

        $this->assertSame('Test', $enum::getName(1));
        $this->assertSame('Undefined', $enum::getName(999));
    }

    public function testGetEmoji(): void
    {
        $enum = new class extends AbstractEnum {
            protected static array $emojis = [1 => '🔥'];
            public static function toArray(): array
            {
                return [1];
            }
        };

        $this->assertSame('🔥', $enum::getEmoji(1));
        $this->assertSame('❓', $enum::getEmoji(999));
    }
} 