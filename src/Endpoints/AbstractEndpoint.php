<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Interfaces\EndpointInterface;

abstract class AbstractEndpoint implements EndpointInterface
{
    protected string $endpoint;
    protected array $params;
    protected array $query;

    /**
     * @param string $endpoint
     * @param array<string, mixed> $params
     * @param array<string, mixed> $query
     */
    public function __construct(string $endpoint, array $params = [], array $query = [])
    {
        $this->endpoint = $endpoint;
        $this->params = $params;
        $this->query = $query;
    }

    public function getPath(string $language): string
    {
        $path = $this->endpoint;

        if (!empty($this->params)) {
            $path .= '/' . implode('/', $this->params);
        }

        if (!empty($this->query)) {
            if (array_key_exists('language', $this->query)) {
                $this->query['language'] = $language;
            }
            $path .= '?' . http_build_query($this->query);
        }

        return $path;
    }
} 