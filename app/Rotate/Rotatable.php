<?php

namespace App\Rotate;

use App\Direction;

interface Rotatable
{
    public function getDirection(): Direction;
    public function setDirection(Direction $direction): void;
    public function getAngularVelocity(): int;
    public function getDirectionsNumber(): int;
}