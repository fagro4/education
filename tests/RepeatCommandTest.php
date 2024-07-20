<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Interfaces\Command;
use App\Queue\RepeatCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RepeatCommandTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCommandExecutingStarts()
    {
        /** @var Command&MockObject $targetCommand */
        $targetCommand = $this->createStub(Command::class);
        $targetCommand->expects($this->once())->method('execute');

        $repeatCommand = new RepeatCommand($targetCommand);
        $repeatCommand->execute();
    }
}