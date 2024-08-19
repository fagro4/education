<?php

namespace App\Interfaces;

use Exception;

interface ExceptionHandlerInterface
{
    public function handle(Command $command, Exception $exception): void;
}