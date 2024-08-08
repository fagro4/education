<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Interfaces\Command;
use App\Queue\RepeatAndLogExceptionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class RepeatAndLogExceptionHandlerTest extends TestCase
{
    /**
     * @throws \Throwable
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRestartAndLoggingWorked()
    {
        $exception = new Exception('test');

        /** @var Command&MockObject $targetCommand */
        $targetCommand = $this->createMock(Command::class);
        $targetCommand->expects($this->once())->method('execute');
        $targetCommand->method('execute')->willThrowException($exception);

        /** @var LoggerInterface&MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $command = new RepeatAndLogExceptionHandler($exception, $targetCommand, $logger);
        $this->expectException(Exception::class);
        $command->execute();
    }
}