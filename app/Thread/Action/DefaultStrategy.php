<?php

namespace App\Thread\Action;

use App\Interfaces\ReceiverInterface;
use App\Interfaces\ThreadActionStrategy;
use App\Thread\ThreadInterface;

class DefaultStrategy implements ThreadActionStrategy
{

    public function action(ReceiverInterface $receiver, ThreadInterface $thread): void
    {
        $command = $receiver->receive();
        $command->execute();
    }
}