<?php

namespace App\Interfaces;

interface MiddlewareChain extends Middleware
{
    public function next(MiddlewareChain $handler): MiddlewareChain;
}