<?php

namespace App\Queue;

use App\Interfaces\Command;
use Psr\Log\LoggerInterface;
use Throwable;

class DoubleRepeatAndLogExceptionHandler implements Command
{
    public function __construct(
        private Throwable $exception,
        private Command $targetCommand,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function execute(): void
    {
        $doubleRepeatCommand = new DoubleRepeatCommand($this->targetCommand);
        try {
            $doubleRepeatCommand->execute();
        } catch (Throwable $e) {
            (new RepeatAndLogExceptionHandler($e, $doubleRepeatCommand, $this->logger))->execute();
        }
    }
}
