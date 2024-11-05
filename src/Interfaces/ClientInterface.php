<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Interfaces;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Jokod\Impactco2Php\Endpoints\Endpoint;
use Psr\Log\LoggerInterface;

interface ClientInterface
{
    /**
     * Execute une requête vers l'API
     *
     * @param Endpoint $endpoint
     * @param array<string, mixed> $options
     * @return mixed
     */
    public function execute(Endpoint $endpoint, array $options = []);

    /**
     * Définit la clé API
     */
    public function setApiKey(string $apiKey): void;

    /**
     * Récupère la clé API
     */
    public function getApiKey(): ?string;

    /**
     * Définit la langue
     */
    public function setLanguage(string $language): void;

    /**
     * Récupère la langue
     */
    public function getLanguage(): string;

    /**
     * Définit le client HTTP
     */
    public function setHttpClient(GuzzleClientInterface $client): void;

    /**
     * Récupère le client HTTP
     */
    public function getHttpClient(): GuzzleClientInterface;

    /**
     * Définit le logger
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Récupère le logger
     */
    public function getLogger(): LoggerInterface;
} 