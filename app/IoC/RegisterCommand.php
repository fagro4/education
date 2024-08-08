<?php

namespace App\IoC;

use App\Interfaces\Command;
use Closure;

class RegisterCommand implements Command
{

    public function __construct(
        private string $key,
        private Closure $registerCallBack,
        private Closure $cb,
        private array $attrs
    ) {
    }

    public function execute(): void
    {
        ($this->registerCallBack)($this->key, $this->cb);
    }
}