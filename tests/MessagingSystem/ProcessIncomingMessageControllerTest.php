<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use Amp\Cancellation;
use Amp\Sync\Channel;
use App\Controllers\ProcessIncomingMessage;
use App\Exceptions\Command\NotFoundException as CommandNotFoundException;
use App\Exceptions\Game\Object\NotFoundException as ObjectNotFoundException;
use App\Exceptions\NotFoundException;
use App\Interfaces\Command;
use App\Interfaces\IncommingMessage;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use App\Move\Movable;
use App\Thread\SoftStopThreadCommand;
use App\Thread\StopThreadCommand;
use App\Thread\Thread;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class ProcessIncomingMessageControllerTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    private SenderInterface $sender;
    private Thread $thread;

    public function testUndefinedGame()
    {
        /** @var IncommingMessage&MockObject $message */
        $message = $this->createMock(IncommingMessage::class);
        $message->method('getGameId')->willReturn("0");

        $this->expectException(NotFoundException::class);
        $controller = new ProcessIncomingMessage;
        $controller->handle($message);
        $this->proccessGame();
    }

    public function testUndefinedObject(): void
    {
        /** @var IncommingMessage&MockObject $message */
        $message = $this->createMock(IncommingMessage::class);
        $message->method('getGameId')->willReturn(self::GAME_UID);
        $message->method('getObjectId')->willReturn("undefined_object_id");

        $this->expectException(ObjectNotFoundException::class);
        $controller = new ProcessIncomingMessage;
        $controller->handle($message);
        $this->proccessGame();
    }

    public function testUndefinedCommand(): void
    {
        /** @var IncommingMessage&MockObject $message */
        $message = $this->createMock(IncommingMessage::class);
        $message->method('getGameId')->willReturn(self::GAME_UID);
        $message->method('getObjectId')->willReturn("1");
        $message->method('getCommandCode')->willReturn("undefined_command_code");

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", "1", $movableMock);

        $this->expectException(CommandNotFoundException::class);
        $controller = new ProcessIncomingMessage;
        $controller->handle($message);
        $this->proccessGame();
    }

    public function testProccessMessage(): void
    {
        /** @var IncommingMessage&MockObject $message */
        $message = $this->createMock(IncommingMessage::class);
        $message->method('getGameId')->willReturn(self::GAME_UID);
        $message->method('getObjectId')->willReturn("1");
        $message->method('getCommandCode')->willReturn("move");

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->expects($this->once())->method('getVelocity');

        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", "1", $movableMock);

        $controller = new ProcessIncomingMessage;
        $controller->handle($message);
        $this->proccessGame();
    }

    private function proccessGame()
    {
        $this->sender->send(new SoftStopThreadCommand($this->thread));
        $this->thread->run(
            $this->createMock(Channel::class),
            $this->createMock(Cancellation::class)
        );
    }

    protected function setUp(): void
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

        /** @var SenderInterface $sender */
        $this->sender = IoC::resolve(
            'Sender.Create',
            $queue
        );

        /** @var Thread $thread */
        $this->thread = IoC::resolve(
            'Thread.Create',
            $receiver
        );

        IoC::resolve('Game.Register', self::GAME_UID, $this->sender);
    }
}