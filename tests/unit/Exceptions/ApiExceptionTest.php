<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Exceptions;

use Jokod\Impactco2Php\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class ApiExceptionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $message = 'Test message';
        $context = ['key' => 'value'];
        $code = 123;

        $exception = new ApiException($message, $context, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($context, $exception->getContext());
    }

    public function testEmptyContext(): void
    {
        $exception = new ApiException('Test');
        $this->assertSame([], $exception->getContext());
    }
} 