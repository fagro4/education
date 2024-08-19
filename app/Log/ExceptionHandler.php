<?php

namespace App\Log;

use App\Interfaces\Command;
use App\Queue\CommandQueueInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ExceptionHandler implements Command
{
    public function __construct(
        private CommandQueueInterface $queue,
        private Throwable $exception,
        private LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        $this->queue->push(
            new LogExceptionCommand($this->exception, $this->logger)
        );
    }
}