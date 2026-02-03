<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Interfaces\EndpointInterface;

class Endpoint implements EndpointInterface
{
    /**
     * @param string $endpoint File of the endpoint
     * @param mixed[] $params Parameters of the endpoint (ex: /{param})
     * @param mixed[] $query Query parameters of the endpoint (ex: ?param=value)
     */
    public function __construct(private string $endpoint, private array $params = [], private array $query = [])
    {
    }

    /**
     * Get the path of the endpoint
     * 
     * @param string $language Language of the endpoint
     * @return string
     */
    public function getPath(string $language): string
    {
        $path = $this->endpoint;

        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $path .= '/' . $param;
            }
        }

        if (!empty($this->query)) {
            $queryParams = $this->query;
            
            if (array_key_exists('language', $queryParams)) {
                $queryParams['language'] = $language;
            }

            $queryParams = array_filter($queryParams, fn($value) => $value !== null);

            if (!empty($queryParams)) {
                $path .= '?' . http_build_query($queryParams);
            }
        }

        return $path;
    }
}
