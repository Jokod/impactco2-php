<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

/**
 * Represents usage information for an item
 * 
 * @immutable
 */
final readonly class Usage
{
    /**
     * @param float $perYear Usage per year
     * @param int $defaultYears Default number of years of usage
     * 
     * @throws InvalidArgumentException If any parameter is invalid
     */
    public function __construct(
        private float $perYear,
        private int $defaultYears
    ) {
        if ($this->perYear < 0) {
            throw new InvalidArgumentException('Usage per year cannot be negative');
        }

        if ($this->defaultYears <= 0) {
            throw new InvalidArgumentException('Default years must be a positive integer');
        }
    }

    /**
     * Get the usage per year
     *
     * @return float
     */
    public function getPerYear(): float
    {
        return $this->perYear;
    }

    /**
     * Get the default number of years
     *
     * @return int
     */
    public function getDefaultYears(): int
    {
        return $this->defaultYears;
    }

    /**
     * Create a Usage instance from API response data.
     * Accepts both camelCase (perYear, defaultYears) and lowercase (peryear, defaultyears) as returned by the API.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $perYear = $data['perYear'] ?? $data['peryear'] ?? 0.0;
        $defaultYears = $data['defaultYears'] ?? $data['defaultyears'] ?? 1;

        return new self(
            perYear: (float) $perYear,
            defaultYears: (int) $defaultYears
        );
    }

    /**
     * Convert the usage to an array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'perYear'      => $this->perYear,
            'defaultYears' => $this->defaultYears,
        ];
    }
}
