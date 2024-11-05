<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Interfaces\EndpointInterface;

abstract class Endpoint implements EndpointInterface
{
    /**
     * @param string $endpoint Nom du endpoint
     * @param array<string, mixed> $params Paramètres du endpoint (ex: /{param})
     * @param array<string, mixed> $query Paramètres de requête (ex: ?param=value)
     */
    public function __construct(
        protected string $endpoint,
        protected array $params = [],
        protected array $query = []
    ) {
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

            // Filtrer les valeurs null et encoder correctement les paramètres
            $queryParams = array_filter($this->query, function ($value) {
                return $value !== null;
            });

            if (!empty($queryParams)) {
                $queryString = http_build_query($this->processQueryValues($queryParams));
                $path .= '?' . str_replace(['%2C', '%5B0%5D'], [',', ''], $queryString);
            }
        }

        return $path;
    }

    /**
     * Traite les valeurs des paramètres de requête pour gérer les tableaux
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function processQueryValues(array $params): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $result[$key] = implode(',', $value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
