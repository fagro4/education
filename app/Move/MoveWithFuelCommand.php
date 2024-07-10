<?php

namespace App\Move;

use App\Fuel\BurnFuelCommand;
use App\Fuel\CheckFuelCommand;
use App\Fuel\Fuelable;
use App\Interfaces\Command;
use App\MacroCommand;
use App\Move\Movable;
use App\Move\MoveCommand;

class MoveWithFuelCommand implements Command
{
    private Command $macroCommand;

    public function __construct(Movable $movableTarget, Fuelable $fuelableTarget)
    {
        $this->macroCommand = new MacroCommand(
            new CheckFuelCommand($fuelableTarget),
            new MoveCommand($movableTarget),
            new BurnFuelCommand($fuelableTarget)
        );
    }

    public function execute(): void
    {
        $this->macroCommand->execute();
    }
}