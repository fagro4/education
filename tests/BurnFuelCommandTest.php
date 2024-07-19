<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Fuel\BurnFuelCommand;
use App\Fuel\Fuelable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BurnFuelCommandTest extends TestCase
{
    public function testSpendFuel()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willReturn(20);
        $mock->method('getSpendVelocity')->willReturn(11);

        $testcase = $this;
        $mock->method('setFuel')->willReturnCallback(function (int $newAmount) use ($testcase) {
            $testcase->assertEquals($newAmount, 9, 'Ошибка в списании топлива');
        });

        $command = new BurnFuelCommand($mock);
        $command->execute();
    }

    public function testCantGetFuel()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willThrowException(new TypeError);
        $mock->method('getSpendVelocity')->willReturn(11);

        $command = new BurnFuelCommand($mock);
        $this->expectException(TypeError::class);
        $command->execute();
    }

    public function testCantGetSpendVelocity()
    {
        /** @var Fuelable&MockObject $mock */
        $mock = $this->createMock(Fuelable::class);
        $mock->method('getFuel')->willReturn(2);
        $mock->method('getSpendVelocity')->willThrowException(new TypeError);

        $command = new BurnFuelCommand($mock);
        $this->expectException(TypeError::class);
        $command->execute();
    }
}