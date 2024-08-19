<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Interfaces\SenderInterface;
use App\Queue\CommandQueueInterface;

class Sender implements SenderInterface
{
    public function __construct(private CommandQueueInterface $queue)
    {
    }

    public function send(Command $command): void
    {
        $this->queue->push($command);
    }
}