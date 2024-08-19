<?php

namespace App\Interfaces;

use App\Vector;

interface Cell
{
    function getWidth(): int;
    function setWidth(int $width);
    function getHeight(): int;
    function setHeight(int $height);
    function getPosition(): Vector;
    function setPosition(Vector $position);
}