<?php

namespace App\Thread\State\Command;

use App\Interfaces\Command;
use App\Interfaces\ThreadInterface;
use App\IoC\IoC;

class HardStopCommand implements Command
{
    public function __construct(private ThreadInterface $thread)
    {
    }

    public function execute(): void
    {
        IoC::resolve('Thread.State.Set', $this->thread, null);
    }
}