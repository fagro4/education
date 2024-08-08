<?php

namespace App\Queue\Async;

use App\Interfaces\Command;
use App\IoC\IoC;
use App\Queue\CommandQueueInterface;

class CommandQueue implements CommandQueueInterface
{
    /**
     * @param Command[] $queue
     * @return void
     */
    public function __construct(private array $queue, private int $delay)
    {
    }

    public function take(): Command
    {
        $command = array_shift($this->queue);
        if (empty($command)) {
            return IoC::resolve('Command.Await', $this->delay);
        } else {
            return $command;
        }
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