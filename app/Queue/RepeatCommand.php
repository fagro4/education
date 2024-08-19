<?php

namespace App\Queue;

use App\Interfaces\Command;

class RepeatCommand implements Command
{
    public function __construct(private Command $target)
    {
    }

    public function execute(): void
    {
        $this->target->execute();
    }
}