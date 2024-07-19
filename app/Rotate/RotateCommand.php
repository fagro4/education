<?php

namespace App\Rotate;

use App\Interfaces\Command;

class RotateCommand implements Command
{
    private Rotatable $target;

    public function __construct(Rotatable $target)
    {
        $this->target = $target;
    }

    public function execute(): void
    {
        $this->target->setDirection(
            $this->target->getDirection()->next($this->target->getAngularVelocity())
        );
    }
}