<?php

namespace App\Fuel;

use App\Exceptions\CommandException;
use App\Fuel\Fuelable;
use App\Interfaces\Command;

class CheckFuelCommand implements Command
{
    private Fuelable $target;

    public function __construct(Fuelable $target)
    {
        $this->target = $target;
    }

    /**
     * @throws CommandException
     * @return void
     */
    public function execute(): void
    {
        if ($this->target->getFuel() < $this->target->getSpendVelocity()) {
            throw new CommandException("Недостаточно остатка топлива!");
        }
    }
}