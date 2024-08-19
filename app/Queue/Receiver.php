<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Interfaces\ReceiverInterface;
use App\IoC\IoC;
use App\Queue\CommandQueueInterface;

class Receiver implements ReceiverInterface
{
    public function __construct(private CommandQueueInterface $queue)
    {
    }

    public function receive(): ?Command
    {
        return $this->queue->take();
    }

    public function push(Command $command): void
    {
        $this->queue[] = $command;
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }
}