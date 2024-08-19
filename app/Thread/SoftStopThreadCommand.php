<?php

namespace App\Thread;

use App\Interfaces\Command;
use App\Thread\Action\SoftStopStrategy;

class SoftStopThreadCommand implements Command
{
    public function __construct(private ThreadInterface $thread)
    {
    }

    public function execute(): void
    {
        $this->thread->setActionStrategy(new SoftStopStrategy);
    }
}