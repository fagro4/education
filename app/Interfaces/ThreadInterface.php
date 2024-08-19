<?php

namespace App\Interfaces;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;

interface ThreadInterface extends Task
{
    public function run(Channel $channel, Cancellation $cancellation): mixed;
    public function setState(State $state): self;
}