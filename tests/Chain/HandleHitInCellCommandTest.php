<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Field\GenerateGridCommand;
use App\Field\HandleHitInCellCommand;
use App\Interfaces\Command;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use App\Move\Movable;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class HandleHitInCellCommandTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    const GRID_KEY = 'BASE';
    private SenderInterface $sender;

    public function testObjectInCell()
    {
        $objectUid = "1";

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(100, 200));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", $objectUid, $movableMock);
        IoC::resolve('Game.Object.Cell.Set', self::GAME_UID, self::GRID_KEY, $objectUid, 1);

        $handleHitCommand = new HandleHitInCellCommand(self::GAME_UID, self::GRID_KEY, $objectUid);
        $handleHitCommand->execute();

        $cellObjects = IoC::resolve("Game." . self::GAME_UID . ".Storage.Grid." . self::GRID_KEY . ".Objects.Get", 1);
        $this->assertContains($objectUid, $cellObjects);
    }

    public function testObjectInNewCell()
    {
        $objectUid = "2";

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(100, 200));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", $objectUid, $movableMock);

        IoC::resolve('Game.Object.Cell.Set', self::GAME_UID, self::GRID_KEY, $objectUid, 1);
        $handleHitCommand = new HandleHitInCellCommand(self::GAME_UID, self::GRID_KEY, $objectUid);
        $handleHitCommand->execute();

        //Проверяем, что объект попал в 1 ячейку
        $cellObjects = IoC::resolve("Game." . self::GAME_UID . ".Storage.Grid." . self::GRID_KEY . ".Objects.Get", 1);
        IoC::resolve('Command.Create.CompareObjects', self::GAME_UID, self::GRID_KEY , $objectUid)->execute();
        $this->assertContains($objectUid, $cellObjects);

        IoC::resolve('Game.Object.Cell.Set', self::GAME_UID, self::GRID_KEY, $objectUid, 2);
        $handleHitCommand->execute();

        $cell1Objects = IoC::resolve(
            "Game." . self::GAME_UID . ".Storage.Grid." . self::GRID_KEY . ".Objects.Get",
            1
        );
        $cell2Objects = IoC::resolve(
            "Game." . self::GAME_UID . ".Storage.Grid." . self::GRID_KEY . ".Objects.Get",
            2
        );

        //Проверяем, что объект пропал из ячейки #1 и перешел в ячейку #2
        $this->assertNotContains($objectUid, $cell1Objects);
        $this->assertContains($objectUid, $cell2Objects);
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

        /** @var SenderInterface $sender */
        $this->sender = IoC::resolve(
            'Sender.Create',
            $queue
        );

        IoC::resolve('Game.Register', self::GAME_UID, $this->sender);

        $generateGridCommand = new GenerateGridCommand(self::GAME_UID, self::GRID_KEY);
        $generateGridCommand->execute();
    }
}