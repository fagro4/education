<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Interfaces\Command;
use App\Log\LogExceptionCommand;
use App\Log\LogExceptionHandler;
use App\Queue\CommandQueueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LogExceptionHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLogExceptionCommandIsPushedToQueue()
    {
        $_self = $this;
        $exception = new Exception('Hello World');

        /** @var LoggerInterface&MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);

        /** @var CommandQueueInterface&MockObject $queue */
        $queue = $this->createStub(CommandQueueInterface::class);
        $queue
            ->method('push')
            ->willReturnCallback(function (Command $command) use ($_self) {
                $_self->assertInstanceOf(LogExceptionCommand::class, $command);
            });

        $command = new LogExceptionHandler($queue, $exception, $logger);
        $command->execute();
    }
}