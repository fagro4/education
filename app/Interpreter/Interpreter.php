<?php

namespace App\Interpreter;

interface Interpreter
{
    public function interpret(): mixed;
}