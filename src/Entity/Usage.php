<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Entity;

class Usage
{
    private float $perYear;

    private int $defaultYears;

    /**
     * Get the value of perYear
     *
     * @return float
     */
    public function getPerYear(): float
    {
        return $this->perYear;
    }

    /**
     * Set the value of perYear
     *
     * @return self
     */
    public function setPerYear(float $perYear): self
    {
        $this->perYear = $perYear;

        return $this;
    }

    /**
     * Get the value of defaultYears
     *
     * @return int
     */
    public function getDefaultYears(): int
    {
        return $this->defaultYears;
    }

    /**
     * Set the value of defaultYears
     *
     * @return self
     */
    public function setDefaultYears(int $defaultYears): self
    {
        $this->defaultYears = $defaultYears;

        return $this;
    }
}
