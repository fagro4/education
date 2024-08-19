<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Queue\CommandQueueInterface;

class CommandQueue implements CommandQueueInterface
{
    /**
     * @param Command[] $queue
     * @return void
     */
    public function __construct(private array $queue)
    {
    }

    public function take(): ?Command
    {
        return $this->isEmpty() ? null : array_shift($queue);
    }

    public function push(Command $command): void
    {
        $this->queue[] = $command;
    }

    public function isEmpty(): bool
    {
        return count($this->queue) === 0;
    }
}