<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Field\GenerateGridCommand;
use App\Interfaces\Command;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class GenerateGridCommandTest extends TestCase
{
    const GAME_UID = '00000000-0000-0000-0000-000000000001';
    private SenderInterface $sender;

    public function testGridGenerate()
    {
        $gridKey = 'BASE';

        $generateGridCommand = new GenerateGridCommand(self::GAME_UID, $gridKey);
        $generateGridCommand->execute();

        $cells = IoC::resolve('Game.' . self::GAME_UID . '.Storage.Grid.' . $gridKey);
        $this->assertEquals(48, count($cells));
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
    }
}