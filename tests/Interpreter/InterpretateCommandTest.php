<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Controllers\ProcessIncomingMessage;
use App\Exceptions\NotFoundException;
use App\Exceptions\PermissionDenied;
use App\Interfaces\Command;
use App\Interfaces\IncommingMessage;
use App\Interfaces\Order;
use App\Interfaces\ReceiverInterface;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use App\Queue\CommandQueueInterface;
use App\Thread\Thread;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use App\GameObject;
use App\Interfaces\UObject;
use App\Interfaces\UserContextInterface;
use App\Interpreter\InterpretateCommand;
use App\Move\Movable;
use App\Vector;

final class InterpretateCommandTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    private SenderInterface $sender;

    public function testInterpretateStartMoveOrder()
    {
        /** @var Order&MockObject $order */
        $order = $this->createMock(Order::class);
        $order->method('getActionCode')->willReturn('StartMove');
        $order->method('getObjectId')->willReturn('1');
        $order->method('getGameId')->willReturn(self::GAME_UID);
        $order->method('getParams')->willReturn([2]);

        /** @var UserContextInterface&MockObject $userContext */
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getLogin')->willReturn('test');

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->expects($this->once())->method('getVelocity');
        $movableMock->expects($this->once())->method('setVelocity')->willReturnCallback(function (Vector $v) {
            $this->assertEquals($v->getX(), 2);
        });

        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", "1", $movableMock, $userContext);
        $interpretateCommand = new InterpretateCommand($order, $userContext);
        $interpretateCommand->execute();
    }

    public function testInterpretateStopMoveOrder()
    {
        /** @var Order&MockObject $order */
        $order = $this->createMock(Order::class);
        $order->method('getActionCode')->willReturn('StopMove');
        $order->method('getObjectId')->willReturn('1');
        $order->method('getGameId')->willReturn(self::GAME_UID);
        $order->method('getParams')->willReturn([2]);

        /** @var UserContextInterface&MockObject $userContext */
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getLogin')->willReturn('test');

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willReturn(new Vector(3, 2));
        $movableMock->expects($this->once())->method('setVelocity')->willReturnCallback(function (Vector $v) {
            $this->assertEquals($v->getX(), 0);
            $this->assertEquals($v->getY(), 0);
        });

        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", "1", $movableMock, $userContext);
        $interpretateCommand = new InterpretateCommand($order, $userContext);
        $interpretateCommand->execute();
    }

    public function testAccessDenied()
    {
        /** @var Order&MockObject $order */
        $order = $this->createMock(Order::class);
        $order->method('getActionCode')->willReturn('StartMove');
        $order->method('getObjectId')->willReturn('1');
        $order->method('getGameId')->willReturn(self::GAME_UID);
        $order->method('getParams')->willReturn([2]);

        /** @var UserContextInterface&MockObject $objectOwnerContext */
        $objectOwnerContext = $this->createMock(UserContextInterface::class);
        $objectOwnerContext->method('getLogin')->willReturn('owner_login');

        /** @var UserContextInterface&MockObject $anotherUserContext */
        $anotherUserContext = $this->createMock(UserContextInterface::class);
        $anotherUserContext->method('getLogin')->willReturn('another_user_login');

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        IoC::resolve(
            "Game." . self::GAME_UID . ".Objects.Register",
            "1",
            $movableMock,
            $objectOwnerContext
        );

        $interpretateCommand = new InterpretateCommand($order, $anotherUserContext);
        $this->expectException(PermissionDenied::class);
        $interpretateCommand->execute();
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

        IoC::resolve('Game.Register', self::GAME_UID, $this->sender);
    }
}