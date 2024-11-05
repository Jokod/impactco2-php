<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Jokod\Impactco2Php\Endpoints\Endpoint;
use Jokod\Impactco2Php\Enum\LanguagesEnum;
use Jokod\Impactco2Php\Exceptions\ApiException;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use Jokod\Impactco2Php\Interfaces\ClientInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Client principal pour l'API Impact CO2.
 *
 * Cette classe gère les appels à l'API Impact CO2 et fournit une interface
 * pour accéder aux différents endpoints.
 *
 * @author Jordan SAMOUH <jordansamouh@gmail.com>
 */
final class Client implements ClientInterface
{
    private const API_VERSION = 'v1';
    private const API_BASE_PATH = 'https://impactco2.fr/api/' . self::API_VERSION . '/';
    private const DEFAULT_HEADERS = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    private ?string $apiKey = null;
    private string $language;
    private array $config;
    private ?GuzzleClientInterface $httpClient = null;
    private ?LoggerInterface $logger = null;

    /**
     * Constructeur du client Impact CO2.
     *
     * @param array<string, mixed> $config Configuration du client
     *                                     - api_key: (string|null) Clé API pour l'authentification
     *                                     - language: (string) Langue par défaut (fr, en, es, de)
     *                                     - logger: (LoggerInterface|null) Logger personnalisé
     *                                     - base_path: (string) URL de base de l'API
     */
    public function __construct(array $config = [])
    {
        $this->initializeConfig($config);
        $this->initializeApiKey();
        $this->initializeLanguage();
        $this->initializeLogger();
    }

    /**
     * Exécute une requête vers l'API.
     *
     * @param Endpoint $endpoint Point d'entrée de l'API à appeler
     * @param array<string, mixed> $options Options supplémentaires pour la requête
     *
     * @throws ApiException Si une erreur survient lors de l'appel API
     * @return array<string, mixed> Réponse de l'API
     */
    public function execute(Endpoint $endpoint, array $options = []): array
    {
        try {
            $response = $this->getHttpClient()->request(
                'GET',
                $this->buildPath($endpoint),
                $this->buildOptions($options)
            );

            $content = $this->parseResponse($response);

            if ($response->getStatusCode() !== 200) {
                throw new ApiException(
                    $content['issues'] ?? 'Unknown error',
                    ['response' => $content]
                );
            }

            return $content;
        } catch (\Throwable $e) {
            $this->logError($e, $endpoint, $options);
            throw new ApiException($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Définit la clé API pour l'authentification.
     *
     * @param string $apiKey Clé API
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Récupère la clé API actuelle.
     *
     * @return string|null Clé API ou null si non définie
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Définit la langue pour les requêtes.
     *
     * @param string $language Code de langue (fr, en, es, de)
     * @throws InvalidArgumentException Si la langue n'est pas supportée
     */
    public function setLanguage(string $language): void
    {
        if (!in_array($language, LanguagesEnum::toArray(), true)) {
            throw new InvalidArgumentException('Invalid language');
        }
        $this->language = $language;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setHttpClient(GuzzleClientInterface $client): void
    {
        $this->httpClient = $client;
    }

    public function getHttpClient(): GuzzleClientInterface
    {
        if ($this->httpClient === null) {
            $this->httpClient = $this->createDefaultHttpClient();
        }
        return $this->httpClient;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = $this->createDefaultLogger();
        }
        return $this->logger;
    }

    /**
     * Initialise la configuration du client.
     *
     * @param array<string, mixed> $config Configuration fournie
     */
    private function initializeConfig(array $config): void
    {
        $this->config = array_merge([
            'base_path' => self::API_BASE_PATH,
            'api_key' => null,
            'language' => LanguagesEnum::default(),
            'logger' => null,
        ], $config);
    }

    /**
     * Initialise la clé API pour l'authentification.
     */
    private function initializeApiKey(): void
    {
        if (isset($this->config['api_key']) && is_string($this->config['api_key'])) {
            $this->setApiKey($this->config['api_key']);
        }
    }

    /**
     * Initialise la langue pour les requêtes.
     */
    private function initializeLanguage(): void
    {
        if (isset($this->config['language'])) {
            $this->setLanguage($this->config['language']);
        }
    }

    /**
     * Initialise le logger personnalisé.
     */
    private function initializeLogger(): void
    {
        $this->logger = $this->config['logger'] instanceof LoggerInterface 
            ? $this->config['logger'] 
            : $this->createDefaultLogger();
    }

    /**
     * Construit le chemin complet pour une requête API.
     *
     * @param Endpoint $endpoint Point d'entrée de l'API
     * @return string Chemin complet de la requête
     */
    private function buildPath(Endpoint $endpoint): string
    {
        return $this->config['base_path'] . $endpoint->getPath($this->getLanguage());
    }

    /**
     * Construit les options pour la requête HTTP.
     *
     * @param array<string, mixed> $options Options supplémentaires
     * @return array<string, mixed> Options complètes pour la requête
     */
    private function buildOptions(array $options): array
    {
        $headers = self::DEFAULT_HEADERS;

        if ($this->apiKey !== null) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }

        return array_merge_recursive(['headers' => $headers], $options);
    }

    /**
     * Parse la réponse HTTP en tableau.
     *
     * @param mixed $response Réponse HTTP
     * @return array<string, mixed> Contenu de la réponse
     */
    private function parseResponse($response): array
    {
        return json_decode($response->getBody()->getContents(), true) ?? [];
    }

    /**
     * Journalise une erreur survenue lors d'une requête.
     *
     * @param \Throwable $e Exception survenue
     * @param Endpoint $endpoint Point d'entrée concerné
     * @param array<string, mixed> $options Options de la requête
     */
    private function logError(\Throwable $e, Endpoint $endpoint, array $options): void
    {
        $this->getLogger()->error('API request failed', [
            'exception' => $e,
            'endpoint' => $endpoint,
            'options' => $options,
        ]);
    }

    private function createDefaultHttpClient(): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri' => $this->config['base_path'],
            'http_errors' => false,
        ]);
    }

    private function createDefaultLogger(): LoggerInterface
    {
        $logger = new Logger('impactco2-php');
        $logger->pushHandler(new StreamHandler('php://stderr', Logger::NOTICE));
        return $logger;
    }
}
