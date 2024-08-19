<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Direction;
use App\Move\Movable;
use App\Rotate\Rotatable;
use App\Rotate\ChangeVelocityCommand;
use App\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ChangeVelocityCommandTest extends TestCase
{
    public function testImmovableObject()
    {
        /** @var Movable&MockObject $movableMock */
        $movableMock = $this->createMock(Movable::class);
        $movableMock->method('getVelocity')->willReturn(new Vector(0, 0));
        $movableMock->method('getPosition')->willReturn(new Vector(0, 0));

        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirection')->willReturn(new Direction(1, 8));
        $rotatableMock->method('getAngularVelocity')->willReturn(1);
        $rotatableMock->method('getDirectionsNumber')->willReturn(8);

        $testcase = $this;
        $movableMock->method('setVelocity')->willReturnCallback(function (Vector $newVector) use ($testcase) {
            $messagePattern = 'По недвижимому объекту произошло смещение мнгновенной скорости по оси %s';
            $testcase->assertEquals($newVector->getX(), 0, sprintf($messagePattern, 'X'));
            $testcase->assertEquals($newVector->getY(), 0, sprintf($messagePattern, 'Y'));
        });

        $command = new ChangeVelocityCommand($rotatableMock, $movableMock);
        $command->execute();
    }
}