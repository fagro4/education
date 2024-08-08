<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Exceptions\CommandException;
use App\Move\Movable;
use App\Rotate\Rotatable;
use App\Rotate\RotateWithChangeVelocityCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RotateWithChangeVelocityCommandTest extends TestCase
{
    public function testChangeOfDirectionAndVelocityVector()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirectionsNumber')->willReturn(8);
        $rotatableMock->method('getAngularVelocity')->willReturn(1);

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);

        $rotatableMock->expects($this->once())->method('setDirection');
        $movableMock->expects($this->once())->method('setVelocity');

        $command = new RotateWithChangeVelocityCommand($rotatableMock, $movableMock);
        $command->execute();
    }

    public function testVelocityVectorDoesntChangeWhenSetdirectionThrowsException()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirectionsNumber')->willReturn(8);
        $rotatableMock->method('getAngularVelocity')->willReturn(1);
        $rotatableMock->method('setDirection')->willThrowException(new Exception);

        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);

        $rotatableMock->expects($this->once())->method('setDirection');
        $movableMock->expects($this->never())->method('setVelocity');

        $command = new RotateWithChangeVelocityCommand($rotatableMock, $movableMock);
        $this->expectException(CommandException::class);
        $command->execute();
    }
}