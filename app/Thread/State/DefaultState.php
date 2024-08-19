<?php

namespace App\Thread\State;

use App\Interfaces\ReceiverInterface;
use App\Interfaces\State;
use App\Interfaces\ThreadInterface;
use App\IoC\IoC;

class DefaultState implements State
{
    function __construct(private ThreadInterface $thread, private ReceiverInterface $receiver)
    {
    }

    public function handle(): ?State
    {
        if ($this->receiver->isEmpty()) {
            return null;
        }

        $command = $this->receiver->receive();
        $command->execute();

        return IoC::resolve('Thread.State.Get', $this->thread);
    }
}