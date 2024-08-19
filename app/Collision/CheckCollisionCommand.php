<?php

namespace App\Collision;

use App\Interfaces\Command;
use App\Move\Movable;

class CheckCollisionCommand implements Command
{
    public function __construct(
        private Movable $targetObject,
        private Movable $object,
    ) {
    }

    public function execute(): void
    {
    }
}