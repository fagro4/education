<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Interfaces\Command;
use App\Interfaces\ReceiverInterface;
use App\Interfaces\ThreadInterface;
use App\IoC\IoC;
use App\Queue\CommandQueueInterface;
use App\Thread\State\Command\HardStopCommand;
use App\Thread\State\Command\MoveToCommand;
use App\Thread\State\Command\RunCommand;
use App\Thread\State\DefaultState;
use App\Thread\State\MoveToState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StateThreadTest extends TestCase
{
    public function testHardStopCommand()
    {
        /**
         * @var ThreadInterface $thread
         * @var ReceiverInterface $receiver
         * @var CommandQueueInterface $queue
         */
        list($thread, $receiver, $queue) = $this->getInitialObjects();

        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute');

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->expects($this->once())->method('execute');

        $queue->push($command1);
        $queue->push(new HardStopCommand($thread));
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

    /**
     * Проверяем переключение состояний через команды MoveToCommand и RunCommand
     * @return void
     */
    public function testMovetocommandAndRuncommand()
    {
        /**
         * @var ThreadInterface $thread
         * @var ReceiverInterface $receiver
         * @var CommandQueueInterface $queue
         */
        list($thread, $receiver, $queue) = $this->getInitialObjects();

        $defaultState = IoC::resolve('Thread.State.Default', $thread, $receiver);
        $thread->setState($defaultState);

        $refThread = new ReflectionObject($thread);
        $threadStateRef = $refThread->getProperty('state');
        $threadStateRef->setAccessible(true);

        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1
            ->expects(self::exactly(2))
            ->method('execute')
            ->willReturnCallback(
                function () use ($threadStateRef, $thread) {
                    self::assertInstanceOf(DefaultState::class, $threadStateRef->getValue($thread));
                }
            );


        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2
            ->expects(self::once())
            ->method('execute')
            ->willReturnCallback(
                function () use ($threadStateRef, $thread): void {
                    self::assertInstanceOf(MoveToState::class, $threadStateRef->getValue($thread));
                }
            );

        $queue->push($command1);
        $queue->push(new MoveToCommand($thread, $receiver));
        $queue->push($command2);
        $queue->push(new RunCommand($thread, $receiver));
        $queue->push($command1);
        $queue->push(new HardStopCommand($thread));

        /** @var Channel&MockObject $channel */
        $channel = $this->createMock(Channel::class);
        /** @var Cancellation&MockObject $cancellation */
        $cancellation = $this->createMock(Cancellation::class);

        $thread->run($channel, $cancellation);
    }

    public function getInitialObjects(): array
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
            'Thread.State.Create'
        );

        $defaultState = IoC::resolve('Thread.State.Default', $thread, $receiver);
        $thread->setState($defaultState);

        return [$thread, $receiver, $queue];
    }
}