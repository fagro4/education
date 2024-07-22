<?php

namespace App\Move;

use App\Interfaces\Command;
use App\Move\Movable;
use App\Vector;

class MoveCommand implements Command
{
    private Movable $target;

    public function __construct(Movable $target)
    {
        $this->target = $target;
    }

    public function execute(): void
    {
        $this->target->setPosition(Vector::plus(
            $this->target->getVelocity('velocity'),
            $this->target->getPosition('position')
        ));
    }
}