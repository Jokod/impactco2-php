<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php;

/**
 * Représente la réponse normalisée de l'API ImpactCO2.
 * Les données (data) sont hydratées en objets de la librairie selon l'endpoint appelé.
 *
 * @immutable
 */
final readonly class ApiResponse
{
    /**
     * @param mixed $data Données hydratées (objets de la librairie ou tableau brut selon l'endpoint)
     * @param string|null $warning Message d'avertissement éventuel renvoyé par l'API
     */
    public function __construct(
        private mixed $data,
        private ?string $warning = null
    ) {
    }

    /**
     * Retourne les données de la réponse (objets Thematic[], Transport[], ECV, etc. selon l'endpoint).
     *
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Retourne le message d'avertissement éventuel.
     *
     * @return string|null
     */
    public function getWarning(): ?string
    {
        return $this->warning;
    }
}
