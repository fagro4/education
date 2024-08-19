<?php

namespace App\Queue\Async;

use App\Interfaces\Command;
use function Amp\delay;

class AwaitCommand implements Command
{
    public function __construct(private int $timeout)
    {
    }

    public function execute(): void
    {
        delay($this->timeout);
    }
}