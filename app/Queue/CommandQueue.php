<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Queue\CommandQueueInterface;

class CommandQueue implements CommandQueueInterface
{
    private array $queue = [];

    /**
     * @param Command[] $queue
     * @return void
     */
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    public function take(): ?Command
    {
        return array_shift($this->queue);
    }

    public function push(Command $command): void
    {
        $queue[] = $command;
    }
}