<?php

namespace App\Thread;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;
use App\Interfaces\ThreadActionStrategy;

interface ThreadInterface extends Task
{
    public function run(Channel $channel, Cancellation $cancellation): mixed;
    public function stop(): void;
    public function setActionStrategy(ThreadActionStrategy $actionStrategy): void;
}