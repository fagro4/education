<?php

namespace App\Thread\State\Command;

use App\Interfaces\Command;
use App\Interfaces\ReceiverInterface;
use App\Interfaces\ThreadInterface;
use App\IoC\IoC;

class RunCommand implements Command
{
    public function __construct(private ThreadInterface $thread, private ReceiverInterface $receiver)
    {
    }

    public function execute(): void
    {
        $defaultState = IoC::resolve('Thread.State.Default', $this->thread, $this->receiver);
        IoC::resolve('Thread.State.Set', $defaultState);
    }
}