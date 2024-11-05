<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Enum;

abstract class AbstractEnum
{
    /**
     * @var array<int|string, string>
     */
    protected static array $names = [];

    /**
     * @var array<int|string, string> 
     */
    protected static array $emojis = [];

    public static function getName(?int $id): string
    {
        return static::$names[$id] ?? 'Undefined';
    }

    public static function getEmoji(?int $id): string
    {
        return static::$emojis[$id] ?? '❓';
    }

    /**
     * @return array<int|string>
     */
    abstract public static function toArray(): array;
} 