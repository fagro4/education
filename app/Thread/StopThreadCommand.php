<?php

namespace App\Thread;

use App\Interfaces\Command;

class StopThreadCommand implements Command
{
    public function __construct(private ThreadInterface $thread)
    {
    }

    public function execute(): void
    {
        $this->thread->stop();
    }
}