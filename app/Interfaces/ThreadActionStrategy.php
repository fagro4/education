<?php

namespace App\Interfaces;

use App\Thread\ThreadInterface;

interface ThreadActionStrategy
{
    public function action(ReceiverInterface $reciever, ThreadInterface $thread): void;
}