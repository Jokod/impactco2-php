<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;

/**
 * Represents an Environmental Life Cycle (ECV) assessment
 * 
 * @immutable
 */
final readonly class ECV
{
    /**
     * @param string $name The ECV name
     * @param float $ecv The total ECV value
     * @param string $slug The ECV slug
     * @param float $footprint The carbon footprint
     * @param Item[] $items The list of items in the ECV
     * @param Usage $usage The usage information
     * @param float $endOfLife The end of life value
     * 
     * @throws InvalidArgumentException If any parameter is invalid
     */
    public function __construct(
        private string $name,
        private float $ecv,
        private string $slug,
        private float $footprint,
        private array $items,
        private Usage $usage,
        private float $endOfLife
    ) {
        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('ECV name cannot be empty');
        }

        if ($this->ecv < 0) {
            throw new InvalidArgumentException('ECV value cannot be negative');
        }

        if (empty(trim($this->slug))) {
            throw new InvalidArgumentException('ECV slug cannot be empty');
        }

        if ($this->footprint < 0) {
            throw new InvalidArgumentException('Footprint cannot be negative');
        }

        // Validate that all items are actually Item instances
        foreach ($this->items as $item) {
            if (!$item instanceof Item) {
                throw new InvalidArgumentException('All items must be instances of Item');
            }
        }

        if ($this->endOfLife < 0) {
            throw new InvalidArgumentException('End of life value cannot be negative');
        }
    }

    /**
     * Get the ECV name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the total ECV value
     *
     * @return float
     */
    public function getEcv(): float
    {
        return $this->ecv;
    }

    /**
     * Get the ECV slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the carbon footprint
     *
     * @return float
     */
    public function getFootprint(): float
    {
        return $this->footprint;
    }

    /**
     * Get the list of items
     *
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the usage information
     *
     * @return Usage
     */
    public function getUsage(): Usage
    {
        return $this->usage;
    }

    /**
     * Get the end of life value
     *
     * @return float
     */
    public function getEndOfLife(): float
    {
        return $this->endOfLife;
    }

    /**
     * Create an ECV instance from API response data.
     * Accepts "items" or "footprintDetail" (API returns footprintDetail as array of {id, value}).
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $items = [];
        $itemsSource = $data['items'] ?? $data['footprintDetail'] ?? [];
        if (\is_array($itemsSource)) {
            foreach ($itemsSource as $itemData) {
                if (\is_array($itemData)) {
                    $items[] = Item::fromArray($itemData);
                }
            }
        }

        $usageData = $data['usage'] ?? [];
        $usage = \is_array($usageData)
            ? Usage::fromArray($usageData)
            : new Usage(0.0, 1);

        $name = $data['name'] ?? '';
        $ecv = $data['ecv'] ?? 0.0;
        $slug = $data['slug'] ?? '';
        $footprint = $data['footprint'] ?? 0.0;
        $endOfLife = $data['endOfLife'] ?? 0.0;

        return new self(
            name: (string) $name,
            ecv: (float) $ecv,
            slug: (string) $slug,
            footprint: (float) $footprint,
            items: $items,
            usage: $usage,
            endOfLife: (float) $endOfLife
        );
    }

    /**
     * Convert the ECV to an array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'ecv'       => $this->ecv,
            'slug'      => $this->slug,
            'footprint' => $this->footprint,
            'items'     => array_map(fn(Item $item) => $item->toArray(), $this->items),
            'usage'     => $this->usage->toArray(),
            'endOfLife' => $this->endOfLife,
        ];
    }
}
