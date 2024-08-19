<?php

namespace App;

use App\Interfaces\UObject;

class GameObject implements UObject
{
    private array $props = [];

    public function setProperty(string $key, mixed $value): void
    {
        $this->props[$key] = $value;
    }
    public function getProperty(string $key): mixed
    {
        return $this->props[$key];
    }
}