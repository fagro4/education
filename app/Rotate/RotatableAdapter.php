<?php

namespace App\Rotate;

use App\Direction;
use App\Interfaces\UObject;
use App\Rotate\Rotatable;

class RotatableAdapter implements Rotatable
{
    private UObject $targetObject;

    public function __construct(UObject $targetObject)
    {
        $this->targetObject = $targetObject;
    }

    public function getDirection(): Direction
    {
        return $this->targetObject->getProperty('direction');
    }

    public function setDirection(Direction $direction): void
    {
        $this->targetObject->setProperty('direction', $direction);
    }

    public function getAngularVelocity(): int
    {
        return $this->targetObject->getProperty('angularVelocity');
    }

    public function getDirectionsNumber(): int
    {
        return $this->getDirection()->getDirectionsNumber();
    }
}