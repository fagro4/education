<?php

namespace App\Log;

use App\Interfaces\Command;
use Psr\Log\LoggerInterface;
use Throwable;

class LogExceptionCommand implements Command
{
    public function __construct(
        private Throwable $target,
        private LoggerInterface $logger
    ) {
    }

    public function execute(): void
    {
        $this->logger->error(
            $this->target->getMessage(),
            ['exception' => $this->target]
        );
    }
}
