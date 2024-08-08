<?php

namespace App\Thread;

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Interfaces\ReceiverInterface;
use App\Interfaces\ThreadActionStrategy;
use Throwable;

class Thread implements ThreadInterface
{
    private bool $stop = false;

    public function __construct(private ReceiverInterface $receiver, private ThreadActionStrategy $actionStrategy)
    {
    }

    public function setActionStrategy(ThreadActionStrategy $actionStrategy): void
    {
        $this->actionStrategy = $actionStrategy;
    }

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        $channel->send('Thread started');
        $this->stop = false;

        while (!$this->stop) {
            try {
                $this->actionStrategy->action($this->receiver, $this);
            } catch (Throwable $th) {
            }
        }

        $channel->send('Thread stoped');
        return null;
    }

    public function stop(): void
    {
        $this->stop = true;
    }
}