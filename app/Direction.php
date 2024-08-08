<?php

namespace App;

class Direction
{
    private int $direction;
    private int $directionsNumber;

    public function __construct(int $direction = 0, int $directionsNumber = 8)
    {
        $this->direction = $direction;
        $this->directionsNumber = $directionsNumber;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function getDirectionsNumber(): int
    {
        return $this->directionsNumber;
    }

    public function getAngular(): int
    {
        return $this->direction / 360 * $this->directionsNumber;
    }

    public function next(int $angularVelocity): self
    {
        $this->direction = ($this->direction + $angularVelocity) % $this->directionsNumber;
        return $this;
    }
}