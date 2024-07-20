<?php

namespace App\Queue;

use App\Interfaces\Command;

interface CommandQueueInterface
{
    public function take(): ?Command;
    public function push(Command $command): void;
}