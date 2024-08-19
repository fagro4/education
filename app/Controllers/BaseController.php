<?php

namespace App\Controllers;

use App\Interfaces\Controller;
use App\Interfaces\IncommingMessage;
use App\Interfaces\MiddlewareChain;

abstract class BaseController implements Controller
{
    private MiddlewareChain $chain;

    public function handle(IncommingMessage $message): void
    {
        if (!empty($this->chain)) {
            $this->chain->handle($message);
        }
        $this->handleInternal($message);
    }

    public function middleware(MiddlewareChain $middleware): MiddlewareChain
    {
        $this->chain = $middleware;
        return $this->chain;
    }

    abstract protected function handleInternal(IncommingMessage $message): void;
}