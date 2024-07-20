<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\CommandException;
use App\Interfaces\Command;
use App\MacroCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MacroCommandTest extends TestCase
{
    public function testExecuteAllCommands()
    {
        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute');

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->expects($this->once())->method('execute');

        /** @var Command&MockObject $command3 */
        $command3 = $this->createMock(Command::class);
        $command3->expects($this->once())->method('execute');

        $macroCommand = new MacroCommand($command1, $command2, $command3);

        $macroCommand->execute();
    }

    public function testExecutionBreaksOnException()
    {
        /** @var Command&MockObject $command1 */
        $command1 = $this->createMock(Command::class);
        $command1->expects($this->once())->method('execute');

        /** @var Command&MockObject $command2 */
        $command2 = $this->createMock(Command::class);
        $command2->method('execute')->willThrowException(new CommandException);

        /** @var Command&MockObject $command3 */
        $command3 = $this->createMock(Command::class);
        $command3->expects($this->never())->method('execute');

        $macroCommand = new MacroCommand($command1, $command2, $command3);
        $this->expectException(CommandException::class);
        $macroCommand->execute();
    }
}