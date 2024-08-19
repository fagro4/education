<?php

namespace App\Interpreter;

use App\Interfaces\UObject;
use App\IoC\IoC;

class ObjectInterpreter implements Interpreter
{
    public function __construct(private string $objectId, private string $gameId) {
    }

    /**
     * @return UObject
     */
    public function interpret(): mixed {
        return IoC::resolve(
            "Game.$this->gameId.Objects.Get",
            $this->objectId
        );
    }
}