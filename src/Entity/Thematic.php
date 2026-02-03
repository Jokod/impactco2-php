<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

/**
 * Represents a thematic category
 * 
 * @immutable
 */
final readonly class Thematic
{
    /**
     * @param int $id The thematic identifier
     * @param string $name The thematic name
     * @param string $slug The thematic slug
     * 
     * @throws InvalidArgumentException If any parameter is invalid
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $slug
    ) {
        if ($this->id <= 0) {
            throw new InvalidArgumentException('Thematic ID must be a positive integer');
        }

        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('Thematic name cannot be empty');
        }

        if (empty(trim($this->slug))) {
            throw new InvalidArgumentException('Thematic slug cannot be empty');
        }
    }

    /**
     * Get the thematic identifier
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the thematic name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the thematic slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Create a Thematic instance from API response data
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $name = $data['name'] ?? '';
        $slug = $data['slug'] ?? '';

        return new self(
            id: (int) $id,
            name: (string) $name,
            slug: (string) $slug
        );
    }

    /**
     * Convert the thematic to an array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
