<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

class Item
{
    private int $id;

    private float $value;

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of value
     * 
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }
}
