<?php

namespace App\Fuel;

interface Fuelable
{
    public function getFuel(): int;
    public function setFuel(int $fuel): void;
    public function getSpendVelocity(): int;
}