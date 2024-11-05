<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Exceptions;

class ApiException extends Exception
{
    private array $context;

    public function __construct(string $message, array $context = [], int $code = 0)
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }
} 