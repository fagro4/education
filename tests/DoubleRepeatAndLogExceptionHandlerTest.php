<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Interfaces\Command;
use App\Queue\DoubleRepeatAndLogExceptionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class DoubleRepeatAndLogExceptionHandlerTest extends TestCase
{
    /**
     * @throws \Throwable
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testDoubleRestartAndLoggingWorked()
    {
        $exception = new Exception('test');

        /** @var Command&MockObject $targetCommand */
        $targetCommand = $this->createMock(Command::class);
        $targetCommand->expects($this->atLeast(2))->method('execute');
        $targetCommand->method('execute')->willThrowException($exception);

        /** @var LoggerInterface&MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $command = new DoubleRepeatAndLogExceptionHandler($exception, $targetCommand, $logger);
        $this->expectException(Exception::class);
        $command->execute();
    }
}