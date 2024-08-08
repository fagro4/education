<?php

namespace App\Rotate;

use App\Interfaces\Command;
use App\Move\Movable;
use App\Rotate\Rotatable;
use App\Vector;

class ChangeVelocityCommand implements Command
{
    public function __construct(private Rotatable $rotatableTarget, private Movable $movableTarget)
    {
    }

    public function execute(): void
    {
        $angular = $this->rotatableTarget->getAngularVelocity() * 360 / $this->rotatableTarget->getDirectionsNumber();
        $velocity = $this->movableTarget->getVelocity();
        $newVelocity = new Vector(
            $velocity->getX() * cos($angular) - $velocity->getY() * sin($angular),
            $velocity->getY() * cos($angular) + $velocity->getX() * sin($angular)
        );

        $this->movableTarget->setVelocity($newVelocity);
    }
}