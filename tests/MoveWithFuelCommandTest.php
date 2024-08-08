<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\CommandException;
use App\Exceptions\TypeError;
use App\Fuel\Fuelable;
use App\Move\Movable;
use App\Move\MoveWithFuelCommand;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MoveWithFuelCommandTest extends TestCase
{
    public function testPositive()
    {
        /** @var Fuelable&MockObject $fuelableMock */
        $fuelableMock = $this->createMock(Fuelable::class);
        $fuelableMock->method('getFuel')->willReturn(20);
        $fuelableMock->method('getSpendVelocity')->willReturn(10);

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willReturn(new Vector(-7, 3));
        $movableMock->method('getPosition')->willReturn(new Vector(12, 5));


        $fuelableMock->expects($this->atLeastOnce())->method('getFuel');
        $movableMock->expects($this->once())->method('setPosition');
        $fuelableMock->expects($this->once())->method('setFuel');

        $command = new MoveWithFuelCommand($movableMock, $fuelableMock);
        $command->execute();
    }

    public function testObjectDoesNotMoveWithEmptyTank()
    {
        /** @var Fuelable&MockObject $fuelableMock */
        $fuelableMock = $this->createMock(Fuelable::class);
        $fuelableMock->method('getFuel')->willReturn(0);
        $fuelableMock->method('getSpendVelocity')->willReturn(10);

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willReturn(new Vector(-7, 3));
        $movableMock->method('getPosition')->willReturn(new Vector(12, 5));


        $fuelableMock->expects($this->atLeastOnce())->method('getFuel');
        $movableMock->expects($this->never())->method('setPosition');
        $fuelableMock->expects($this->never())->method('setFuel');

        $command = new MoveWithFuelCommand($movableMock, $fuelableMock);
        $this->expectException(CommandException::class);
        $command->execute();
    }


    public function testFuelDoesNotBurnWhenMoveCommandThrowsException()
    {
        /** @var Fuelable&MockObject $fuelableMock */
        $fuelableMock = $this->createMock(Fuelable::class);
        $fuelableMock->method('getFuel')->willReturn(0);
        $fuelableMock->method('getSpendVelocity')->willReturn(10);

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willThrowException(new TypeError);
        $movableMock->method('getPosition')->willReturn(new Vector(12, 5));

        $fuelableMock->expects($this->never())->method('setFuel');

        $command = new MoveWithFuelCommand($movableMock, $fuelableMock);
        $this->expectException(CommandException::class);
        $command->execute();
    }
}