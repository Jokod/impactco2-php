<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests;

use Jokod\Impactco2Php\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new Exception();
        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function testExceptionMessage(): void
    {
        $message = 'Test exception message';
        $exception = new Exception($message);
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $code = 123;
        $exception = new Exception('Test exception message', $code);
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionPrevious(): void
    {
        $previous = new Exception('Previous exception');
        $exception = new Exception('Test exception message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
