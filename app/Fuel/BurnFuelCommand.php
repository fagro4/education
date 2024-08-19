<?php

namespace App\Fuel;

use App\Exceptions\CommandException;
use App\Fuel\Fuelable;
use App\Interfaces\Command;

class BurnFuelCommand implements Command
{
    private Fuelable $target;

    public function __construct(Fuelable $target)
    {
        $this->target = $target;
    }

    public function execute(): void
    {
        $newFuelAmount = $this->target->getFuel() - $this->target->getSpendVelocity();
        if ($newFuelAmount < 0) {
            throw new CommandException("Недостаточно топлива для операции", 1);
        }

        $this->target->setFuel($newFuelAmount);
    }
}