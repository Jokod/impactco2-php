<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

/**
 * Represents a transport mode with its CO2 emissions value
 * 
 * @immutable
 */
final readonly class Transport
{
    /**
     * @param int $id The transport identifier
     * @param string $name The transport name
     * @param float $value The CO2 emissions value in kg CO2e
     * 
     * @throws InvalidArgumentException If any parameter is invalid
     */
    public function __construct(
        private int $id,
        private string $name,
        private float $value
    ) {
        if ($this->id <= 0) {
            throw new InvalidArgumentException('Transport ID must be a positive integer');
        }

        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('Transport name cannot be empty');
        }

        if ($this->value < 0) {
            throw new InvalidArgumentException('Transport value cannot be negative');
        }
    }

    /**
     * Get the transport identifier
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the transport name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the CO2 emissions value
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Create a Transport instance from API response data
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $name = $data['name'] ?? '';
        $value = $data['value'] ?? 0.0;

        return new self(
            id: (int) $id,
            name: (string) $name,
            value: (float) $value
        );
    }

    /**
     * Convert the transport to an array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'value' => $this->value,
        ];
    }
}
