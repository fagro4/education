<?php

namespace App\Thread\Action;

use App\Interfaces\ReceiverInterface;
use App\Interfaces\ThreadActionStrategy;
use App\Thread\ThreadInterface;

class SoftStopStrategy implements ThreadActionStrategy
{
    public function action(ReceiverInterface $receiver, ThreadInterface $thread): void
    {
        if ($receiver->isEmpty()) {
            $thread->stop();
            return;
        }

        $command = $receiver->receive();
        $command->execute();
    }
}