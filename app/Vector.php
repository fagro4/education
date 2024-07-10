<?php

namespace App;

class Vector
{
    private float $x;
    private float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function plus(Vector $v1, Vector $v2): self
    {
        return $v1->setX($v1->getX() + $v2->getX())
            ->setY($v1->getY() + $v2->getY());
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function setX(float $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setY(float $y): self
    {
        $this->y = $y;

        return $this;
    }
}