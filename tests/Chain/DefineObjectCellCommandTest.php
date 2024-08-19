<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Field\DefineObjectCellCommand;
use App\Field\GenerateGridCommand;
use App\Interfaces\Command;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use App\Move\Movable;
use App\Thread\Thread;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class DefineObjectCellCommandTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    private SenderInterface $sender;
    private Thread $thread;

    public function testDefineCell()
    {
        $gridKey = 'BASE';
        $objectUid = "2";

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(100, 200));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", $objectUid, $movableMock);

        $generateGridCommand = new GenerateGridCommand(self::GAME_UID, $gridKey);
        $generateGridCommand->execute();

        $defineCellCommand = new DefineObjectCellCommand(self::GAME_UID, $gridKey, $objectUid);
        $defineCellCommand->execute();

        $cellIndex = IoC::resolve('Game.Object.Cell.Get', self::GAME_UID, $gridKey, $objectUid);
        $this->assertEquals(8, $cellIndex);
    }

    public function testObjectOutOfField()
    {
        $gridKey = 'BASE';
        $objectUid = "2";

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->expects($this->any())->method('getPosition')->willReturn(new Vector(-100, -100));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", $objectUid, $movableMock);

        $generateGridCommand = new GenerateGridCommand(self::GAME_UID, $gridKey);
        $generateGridCommand->execute();

        $defineCellCommand = new DefineObjectCellCommand(self::GAME_UID, $gridKey, $objectUid);
        $defineCellCommand->execute();

        $cellIndex = IoC::resolve('Game.Object.Cell.Get', self::GAME_UID, $gridKey, $objectUid);
        $this->assertEquals(null, $cellIndex);
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