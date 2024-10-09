<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Interfaces\EndpointInterface;

class Endpoint implements EndpointInterface
{
    /**
     * @param string $endpoint
     * @param mixed[] $params
     * @param mixed[] $query
     */
    public function __construct(private string $endpoint, private array $params = [], private array $query = [])
    {
    }

    public function getPath(string $language): string
    {
        if (!empty($this->params)) {
            foreach ($this->params as $param) {
                $this->endpoint .= '/' . $param;
            }
        }

        if (!empty($this->query)) {
            if (array_key_exists('language', $this->query)) {
                $this->query['language'] = $language;
            }

            $this->endpoint .= '?' . http_build_query($this->query);
        }

        return $this->endpoint;
    }
}
