<?php

namespace App\Move;

use App\Direction;
use App\Exceptions\UnexpectedValueException;
use App\Interfaces\UObject;
use App\Move\Movable;
use App\Vector;

class MovableAdapter implements Movable
{
    private UObject $targetObject;

    public function __construct(UObject $targetObject)
    {
        $this->targetObject = $targetObject;
    }

    public function getVelocity(): Vector
    {
        return $this->targetObject->getProperty('velocity');
    }

    public function getPosition(): Vector
    {
        return $this->targetObject->getProperty('position');
    }

    public function setPosition(Vector $vector): void
    {
        $this->targetObject->setProperty('position', $vector);
    }
}