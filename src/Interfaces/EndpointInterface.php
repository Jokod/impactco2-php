<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Interfaces;

interface EndpointInterface
{
    public function getPath(string $language): string;
}
