<?php

namespace App\Interfaces;

interface CommandQueueInterface
{
    public function take(): ?Command;
    public function push(Command $command): void;
}