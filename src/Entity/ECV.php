<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

class ECV
{
    private string $name;

    private float $ecv;

    private string $slug;

    private float $footprint;

    /**
     * @var Item[] $items
     */
    private array $items;

    private Usage $usage;

    private float $endOfLife;

    /**
     * Get the value of name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of ecv
     *
     * @return float
     */
    public function getEcv(): float
    {
        return $this->ecv;
    }

    /**
     * Set the value of ecv
     *
     * @return self
     */
    public function setEcv(float $ecv): self
    {
        $this->ecv = $ecv;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of footprint
     *
     * @return float
     */
    public function getFootprint(): float
    {
        return $this->footprint;
    }

    /**
     * Set the value of footprint
     *
     * @return self
     */
    public function setFootprint(float $footprint): self
    {
        $this->footprint = $footprint;

        return $this;
    }

    /**
     * Get the value of items
     *
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @param Item[] $items
     *
     * @return self
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the value of usage
     */
    public function getUsage(): Usage
    {
        return $this->usage;
    }

    /**
     * Set the value of usage
     *
     * @return self
     */
    public function setUsage(Usage $usage): self
    {
        $this->usage = $usage;

        return $this;
    }

    /**
     * Get the value of endOfLife
     *
     * @return float
     */
    public function getEndOfLife(): float
    {
        return $this->endOfLife;
    }

    /**
     * Set the value of endOfLife
     *
     * @return self
     */
    public function setEndOfLife(float $endOfLife): self
    {
        $this->endOfLife = $endOfLife;

        return $this;
    }
}
