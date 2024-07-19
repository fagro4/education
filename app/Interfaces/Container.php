<?php

namespace App\Interfaces;

interface Container
{
    public static function resolve(string $key, mixed ...$args): mixed;
}