<?php

namespace App\Middlewares;

use App\Interfaces\IncommingMessage;
use App\Interfaces\MiddlewareChain;

abstract class Chain implements MiddlewareChain
{
    private MiddlewareChain $nextHandler;

    public function next(MiddlewareChain $handler): MiddlewareChain
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(IncommingMessage $message)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($message);
        }

        return null;
    }
}