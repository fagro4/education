<?php

namespace App\Thread;

use App\Interfaces\Command;

use function Amp\Parallel\Worker\submit;

class StartThreadCommand implements Command
{
    public function __construct(private ThreadInterface $thread)
    {
    }

    public function execute(): void
    {
        submit($this->thread)->getFuture()->await();
    }
}