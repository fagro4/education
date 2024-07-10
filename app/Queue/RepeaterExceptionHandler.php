<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Queue\CommandQueueInterface;

class RepeaterExceptionHandler implements Command
{
    public function __construct(
        private CommandQueueInterface $queue,
        private Command $target
    ) {
    }

    public function execute(): void
    {
        $this->queue->push(new RepeatCommand($this->target));    }
}