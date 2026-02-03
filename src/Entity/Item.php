<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

/**
 * Represents an item with its associated value
 * 
 * @immutable
 */
final readonly class Item
{
    /**
     * @param int $id The item identifier
     * @param float $value The item value
     * 
     * @throws InvalidArgumentException If any parameter is invalid
     */
    public function __construct(
        private int $id,
        private float $value
    ) {
        if ($this->id <= 0) {
            throw new InvalidArgumentException('Item ID must be a positive integer');
        }

        if ($this->value < 0) {
            throw new InvalidArgumentException('Item value cannot be negative');
        }
    }

    /**
     * Get the item identifier
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the item value
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Create an Item instance from API response data.
     * API may return id as null (e.g. in footprintDetail); 0 is then used as placeholder.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $value = $data['value'] ?? 0.0;
        $idInt = $id === null || $id === '' ? 0 : (int) $id;
        if ($idInt < 0) {
            $idInt = 0;
        }

        return new self(
            id: $idInt === 0 ? 1 : $idInt,
            value: (float) $value
        );
    }

    /**
     * Convert the item to an array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'value' => $this->value,
        ];
    }
}
