<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Interfaces\Command;
use App\Queue\CommandQueueInterface;
use App\Queue\RepeatCommand;
use App\Queue\RepeaterExceptionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RepeaterExceptionHandlerTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRepeatCommandIsPushedToQueue()
    {
        $_self = $this;

        /** @var Command&MockObject $targetCommand */
        $targetCommand = $this->createMock(Command::class);

        /** @var CommandQueueInterface&MockObject $queue */
        $queue = $this->createMock(CommandQueueInterface::class);
        $queue
            ->method('push')
            ->willReturnCallback(function (Command $command) use ($_self) {
                $_self->assertInstanceOf(RepeatCommand::class, $command);
            });

        $command = new RepeaterExceptionHandler($queue, $targetCommand);
        $command->execute();
    }
}