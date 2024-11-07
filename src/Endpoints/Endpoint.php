<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\LanguagesEnum;
use Jokod\Impactco2Php\Interfaces\EndpointInterface;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

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
