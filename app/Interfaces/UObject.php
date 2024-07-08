<?php

namespace App\Interfaces;

interface UObject
{
    public function setProperty(string $key, mixed $value): void;
    public function getProperty(string $key): mixed;
}