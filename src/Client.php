<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Jokod\Impactco2Php\Endpoints\Endpoint;
use Jokod\Impactco2Php\Enum\LanguagesEnum;
use Jokod\Impactco2Php\Exceptions\Exception;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Client
{
    const API_VERSION = 'v1';
    const API_BASE_PATH = 'https://impactco2.fr/api/' . self::API_VERSION . '/';

    /**
     * Bearer token.
     */
    private ?string $apiKey = null;

    private string $language;

    /**
     * Configuration.
     *
     * @var mixed[] $config
     */
    private array $config;

    private ClientInterface $httpClient;

    private LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param mixed[] $config
     */
    public function __construct(array $config = [])
    {
        $this->config = \array_merge([
            'base_path' => self::API_BASE_PATH,
            'api_key'   => null,
            'language'  => LanguagesEnum::default(),
            'logger'    => null,
        ], $config);

        if (!\is_null($this->config['api_key']) && \is_string($this->config['api_key'])) {
            $this->setApiKey($this->config['api_key']);
            unset($this->config['api_key']);
        }

        if (!\is_null($this->config['language'])) {
            $this->setLanguage($this->config['language']);
            unset($this->config['language']);
        }

        if (!is_null($this->config['logger'])) {
            $this->setLogger($this->config['logger']);
            unset($this->config['logger']);
        } else {
            $this->setLogger($this->createDefaultLogger());
            unset($this->config['logger']);
        }
    }

    /**
     * Call the endpoint of the API
     *
     * @param Endpoint $endpoint The config of endpoint to call
     * @param string[] $options The options of the request
     *
     * @return mixed
     */
    public function execute(Endpoint $endpoint, array $options = [])
    {
        $path = $this->config['base_path'] . $endpoint->getPath($this->getLanguage());

        $options = $this->addOptions($options);

        try {
            $response = $this->getHttpClient()->request('GET', $path, $options);
            $content = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() !== 200) {
                $errorMessage = 'Unknown error';
                if (is_array($content)) {
                    $errorMessage = $content['issues'] ?? $errorMessage;
                }

                throw new Exception($errorMessage);
            }

            return $content;
        } catch (\Exception $e) {
            $this->getLogger()->error('Error during request', [
                'exception' => $e,
                'endpoint'  => $endpoint,
                'options'   => $options,
            ]);

            throw new Exception($e->getMessage());
        }
    }

    /**
     * Add options to the request.
     *
     * @param string[] $options
     *
     * @return array<string, mixed>
     */
    public function addOptions(array $options): array
    {
        $options = \array_merge_recursive([
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ], $options);

        if (!is_null($this->apiKey)) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        return $options;
    }

    /**
     * Set a configuration value.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function setConfig(string $name, $value): void
    {
        if (!\is_string($name) || $name === '') {
            throw new InvalidArgumentException('Invalid configuration name');
        }

        $this->config[$name] = $value;
    }

    /**
     * Get a configuration value.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->config[$name] ?? null;
    }

    /**
     * Set the API key.
     *
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get the API key.
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Set the language.
     *
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        if (!\in_array($language, LanguagesEnum::toArray(), true)) {
            throw new Exception('Invalid language');
        }

        $this->language = $language;
    }

    /**
     * Get the language.
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Set the HTTP client.
     *
     * @param ClientInterface $httpClient
     *
     * @return void
     */
    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the HTTP client.
     *
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = $this->createDefaultHttpClient();
        }

        return $this->httpClient;
    }

    /**
     * Set a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get the logger.
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Create a default HTTP client.
     *
     * @return GuzzleClient
     */
    protected function createDefaultHttpClient(): GuzzleClient
    {
        $options = [
            'base_uri'    => $this->config['base_path'],
            'http_errors' => false,
        ];

        return new GuzzleClient($options);
    }

    /**
     * Create a default logger.
     *
     * @return LoggerInterface
     */
    protected function createDefaultLogger()
    {
        $logger = new Logger('impactco2-php');
        $handler = new MonologStreamHandler('php://stderr', Logger::NOTICE);
        $logger->pushHandler($handler);

        return $logger;
    }
}
