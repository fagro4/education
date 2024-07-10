<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\UnexpectedValueException;
use App\Move\MoveCommand;
use App\Move\Movable;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MoveCommandTest extends TestCase
{
    public function testMoveCommandChangePosition()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willReturn(new Vector(-7, 3));
        $movableMock->method('getPosition')->willReturn(new Vector(12, 5));

        $testcase = $this;
        $movableMock->method('setPosition')->willReturnCallback(function (Vector $newPosition) use ($testcase) {
            $testcase->assertEquals($newPosition->getX(), 5, 'Ошибка в смещении объекта по оси X');
            $testcase->assertEquals($newPosition->getY(), 8, 'Ошибка в смещении объекта по оси Y');
        });

        $moveCommand = new MoveCommand($movableMock);
        $moveCommand->execute();
    }

    public function testMoveCommandUndefinedPostion()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willThrowException(new UnexpectedValueException);
        $movableMock->method('getVelocity')->willReturn(new Vector(11, 3));

        $moveCommand = new MoveCommand($movableMock);
        $this->expectException(UnexpectedValueException::class);
        $moveCommand->execute();
    }

    public function testMoveCommandUndefinedVelocity()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getPosition')->willReturn(new Vector(11, 3));
        $movableMock->method('getVelocity')->willThrowException(new UnexpectedValueException);

        $moveCommand = new MoveCommand($movableMock);
        $this->expectException(UnexpectedValueException::class);
        $moveCommand->execute();
    }

    public function testMoveCommandCantSetPosition()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('setPosition')->willThrowException(new Exception);
        $movableMock->method('getPosition')->willReturn(new Vector(11, 3));
        $movableMock->method('getVelocity')->willReturn(new Vector(-3, 2));

        $moveCommand = new MoveCommand($movableMock);
        $this->expectException(Exception::class);
        $moveCommand->execute();
    }
}