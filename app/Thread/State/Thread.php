<?php

namespace App\Thread\State;

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Interfaces\State;
use App\Interfaces\ThreadInterface;
use Throwable;

class Thread implements ThreadInterface
{
    private ?State $state = null;

    public function __construct()
    {
    }

    public function setState(State $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        $channel->send('Thread started');

        while ($this->state !== null) {
            try {
                $this->state = $this->state->handle();
            } catch (Throwable $th) {
            }
        }

        $channel->send('Thread stoped');
        return null;
    }
}