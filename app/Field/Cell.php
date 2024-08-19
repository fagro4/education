<?php

namespace App\Field;

use App\Interfaces\Cell as InterfacesCell;
use App\Interfaces\UObject;
use App\Vector;

class Cell implements InterfacesCell
{
    private UObject $targetObject;

    public function __construct(UObject $targetObject)
    {
        $this->targetObject = $targetObject;
    }

    public function getWidth(): int
    {
        return $this->targetObject->getProperty('width');
    }

    function setWidth(int $width): void
    {
        $this->targetObject->setProperty('width', $width);
    }

    function getHeight(): int
    {
        return $this->targetObject->getProperty('height');
    }

    function setHeight(int $height): void
    {
        $this->targetObject->setProperty('height', $height);
    }

    function getPosition(): Vector
    {
        return $this->targetObject->getProperty('position');
    }

    function setPosition(Vector $position): void
    {
        $this->targetObject->setProperty('position', $position);
    }
}