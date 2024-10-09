<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

class ThematicsEndpoint extends Endpoint
{
    public const ENDPOINT = 'thematiques';

    public function __construct()
    {
        parent::__construct(self::ENDPOINT);
    }
}
