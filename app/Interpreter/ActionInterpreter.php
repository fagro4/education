<?php

namespace App\Interpreter;

use App\IoC\IoC;

class ActionInterpreter implements Interpreter
{
    public function __construct(private string $commandCode, private mixed $object, private array $args) {
    }

    public function interpret(): mixed {
        return IoC::resolve(
            "Action.Factory.Get",
            $this->commandCode,
            $this->object,
            ...$this->args
        );
    }
}