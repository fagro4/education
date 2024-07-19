<?php

namespace App\Rotate;

use App\Interfaces\Command;
use App\MacroCommand;
use App\Move\Movable;

class RotateWithChangeVelocityCommand implements Command
{
    private Command $macroCommand;

    public function __construct(Rotatable $rotatableTarget, Movable $movableTarget)
    {
        $this->macroCommand = new MacroCommand(
            new RotateCommand($rotatableTarget),
            new ChangeVelocityCommand($rotatableTarget, $movableTarget),
        );
    }

    public function execute(): void
    {
        $this->macroCommand->execute();
    }
}