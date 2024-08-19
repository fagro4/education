<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Interfaces\Command;
use App\Interfaces\ReceiverInterface;
use App\IoC\IoC;
use App\Queue\Async\CommandQueue;
use App\Queue\CommandQueueInterface;
use App\Thread\SoftStopThreadCommand;
use App\Thread\StopThreadCommand;
use App\Thread\Thread;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ThreadTest extends TestCase
{
    public function testThreadIsStarted()
    {
        /** @var Command&MockObject $command */
        $command = $this->createMock(Command::class);
        /** @var CommandQueueInterface $queue */
        $queue = IoC::resolve(
            'CommandQueue.Create',
            [$command],
            2
        );

        /** @var ReceiverInterface $receiver */
        $receiver = IoC::resolve(
            'Receiver.Create',
            $queue
        );

        /** @var Thread $thread */
        $thread = IoC::resolve(
            'Thread.Create',
            $receiver
        );

        $queue->push(new SoftStopThreadCommand($thread));
        $processEvents = [];

        /** @var Channel&MockObject $channel */
        $channel = $this->createMock(Channel::class);
        $channel->method('send')
            ->willReturnCallback(function (string $eventText) use (&$processEvents) {
                $processEvents[] = $eventText;
            });

        /** @var Cancellation&MockObject $cancellation */
        $cancellation = $this->createMock(Cancellation::class);


        $thread->run($channel, $cancellation);
        $this->assertEquals("Thread started", $processEvents[0]);
    }

    public function testThreadIsStopedAfterHardStopCommand()
    {
        /** @var CommandQueueInterface $queue */
        $queue = IoC::resolve(
            'CommandQueue.Create',
            [],
            2
        );

        /** @var ReceiverInterface $receiver */
        $receiver = IoC::resolve(
            'Receiver.Create',
            $queue
        );

        /** @var Thread $thread */
        $thread = IoC::resolve(
            'Thread.Create',
            $receiver
        );

        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute');

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->expects($this->never())->method('execute');

        $queue->push($command1);
        $queue->push(new StopThreadCommand($thread));
        $queue->push($command2);
        $processEvents = [];

        /** @var Channel&MockObject $channel */
        $channel = $this->createMock(Channel::class);
        $channel->method('send')
            ->willReturnCallback(function (string $eventText) use (&$processEvents) {
                $processEvents[] = $eventText;
            });

        /** @var Cancellation&MockObject $cancellation */
        $cancellation = $this->createMock(Cancellation::class);

        $thread->run($channel, $cancellation);
        $this->assertEquals("Thread stoped", $processEvents[1]);
    }

    public function testSoftStopCommand()
    {
        /** @var CommandQueueInterface $queue */
        $queue = IoC::resolve(
            'CommandQueue.Create',
            [],
            2
        );

        /** @var ReceiverInterface $receiver */
        $receiver = IoC::resolve(
            'Receiver.Create',
            $queue
        );

        /** @var Thread $thread */
        $thread = IoC::resolve(
            'Thread.Create',
            $receiver
        );

        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute');

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->expects($this->once())->method('execute');

        $queue->push($command1);
        $queue->push(new SoftStopThreadCommand($thread));
        $queue->push($command2);
        $processEvents = [];

        /** @var Channel&MockObject $channel */
        $channel = $this->createMock(Channel::class);
        $channel->method('send')
            ->willReturnCallback(function (string $eventText) use (&$processEvents) {
                $processEvents[] = $eventText;
            });

        /** @var Cancellation&MockObject $cancellation */
        $cancellation = $this->createMock(Cancellation::class);

        $thread->run($channel, $cancellation);
        $this->assertEquals("Thread stoped", $processEvents[1]);
    }

    public function testExceptionsDontBlockExecution()
    {
        /** @var CommandQueueInterface $queue */
        $queue = IoC::resolve(
            'CommandQueue.Create',
            [],
            2
        );

        /** @var ReceiverInterface $receiver */
        $receiver = IoC::resolve(
            'Receiver.Create',
            $queue
        );

        /** @var Thread $thread */
        $thread = IoC::resolve(
            'Thread.Create',
            $receiver
        );

        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute')->willThrowException(new \Exception());

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->expects($this->once())->method('execute');

        $queue->push($command1);
        $queue->push($command2);
        $queue->push(new StopThreadCommand($thread));
        $processEvents = [];

        /** @var Channel&MockObject $channel */
        $channel = $this->createMock(Channel::class);
        $channel->method('send')
            ->willReturnCallback(function (string $eventText) use (&$processEvents) {
                $processEvents[] = $eventText;
            });

        /** @var Cancellation&MockObject $cancellation */
        $cancellation = $this->createMock(Cancellation::class);

        $thread->run($channel, $cancellation);
        $this->assertEquals("Thread stoped", $processEvents[1]);
    }
}