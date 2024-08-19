<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Field\CheckObjectCollisionsCommand;
use App\Interfaces\Command;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use App\Move\Movable;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class CheckObjectCollisionsCommandTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    private SenderInterface $sender;

    public function testObjectsInCommonCell()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(1, 1));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", '1', $movableMock);

        /** @var Movable&MockObject $movableMock2 */
        $movableMock2 = $this->createMock(Movable::class);
        $movableMock2->method('getPosition')->willReturn(new Vector(2, 2));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", '2', $movableMock2);

        (new CheckObjectCollisionsCommand(self::GAME_UID, '1'))->execute();
        (new CheckObjectCollisionsCommand(self::GAME_UID, '2'))->execute();

        $startCellObjects = IoC::resolve(
            "Game." . self::GAME_UID . ".Storage.Grid.BASE.Objects.Get",
            0
        );

        $this->assertContains('1', $startCellObjects);
        $this->assertContains('2', $startCellObjects);

        //Проверим, что создана макрокоманда на проверку коллизий объектов ячейки
        $compareCollisionsCommand = IoC::resolve("Game." . self::GAME_UID . ".Storage.Grid.BASE.Command.Get", 0);
        $this->assertNotEmpty($compareCollisionsCommand);
    }

    public function testCollisionInOffsetGrid()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(120, 120));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", '1', $movableMock);

        /** @var Movable&MockObject $movableMock2 */
        $movableMock2 = $this->createMock(Movable::class);
        $movableMock2->method('getPosition')->willReturn(new Vector(130, 130));
        IoC::resolve("Game." . self::GAME_UID . ".Objects.Register", '2', $movableMock2);

        (new CheckObjectCollisionsCommand(self::GAME_UID, '1'))->execute();
        (new CheckObjectCollisionsCommand(self::GAME_UID, '2'))->execute();


        $cells = IoC::resolve('Game.' . self::GAME_UID . '.Storage.Grid.OFFSET');
        $startCellObjects_baseGrid = IoC::resolve(
            "Game." . self::GAME_UID . ".Storage.Grid.BASE.Objects.Get",
            0
        );
        $startCellObjects_offsetGrid = IoC::resolve(
            "Game." . self::GAME_UID . ".Storage.Grid.OFFSET.Objects.Get",
            0
        );

        $this->assertCount(0, $startCellObjects_baseGrid);
        $this->assertCount(2, $startCellObjects_offsetGrid);
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
        IoC::resolve('Game.Grid.Generate', self::GAME_UID, 'BASE');
        IoC::resolve('Game.Grid.Generate', self::GAME_UID, 'OFFSET', 64, 64);
    }
}