<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\CommandException;
use App\Exceptions\TypeError;
use App\Fuel\CheckFuelCommand;
use App\Fuel\Fuelable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CheckFuelCommandTest extends TestCase
{
    public function testNotEnoughFuelInTheTank()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willReturn(10);
        $mock->method('getSpendVelocity')->willReturn(11);

        $command = new CheckFuelCommand($mock);
        $this->expectException(CommandException::class);
        $command->execute();
    }

    public function testCantGetFuel()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willThrowException(new TypeError);
        $mock->method('getSpendVelocity')->willReturn(11);

        $command = new CheckFuelCommand($mock);
        $this->expectException(TypeError::class);
        $command->execute();
    }

    public function testCantGetSpendVelocity()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willReturn(2);
        $mock->method('getSpendVelocity')->willThrowException(new TypeError);

        $command = new CheckFuelCommand($mock);
        $this->expectException(TypeError::class);
        $command->execute();
    }
}