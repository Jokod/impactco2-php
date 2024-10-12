<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Unit\Exceptions;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testExceptionIsInstanceOfBaseException()
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionMessage()
    {
        $message = 'Invalid argument provided';
        $exception = new InvalidArgumentException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionCode()
    {
        $code = 123;
        $exception = new InvalidArgumentException('Invalid argument', $code);
        $this->assertSame($code, $exception->getCode());
    }
}