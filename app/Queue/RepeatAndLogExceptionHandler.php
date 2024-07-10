<?php

namespace App\Queue;

use App\Interfaces\Command;
use App\Log\LogExceptionCommand;
use Psr\Log\LoggerInterface;
use Throwable;

class RepeatAndLogExceptionHandler implements Command
{
    public function __construct(
        private Throwable $exception,
        private Command $targetCommand,
        private LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        try {
            (new RepeatCommand($this->targetCommand))->execute();
        } catch (Throwable $e) {
            (new LogExceptionCommand($this->exception, $this->logger))->execute();
            throw $e;
        }
    }
}