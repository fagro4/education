
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Direction;
use App\Rotate\Rotatable;
use App\Rotate\RotateCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RotateCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRotateCommandChangeDirection()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirection')->willReturn(new Direction(2, 8));
        $rotatableMock->method('getAngularVelocity')->willReturn(7);

        $testcase = $this;
        $rotatableMock->method('setDirection')->willReturnCallback(function (Direction $newDirection) use ($testcase) {
            $testcase->assertEquals(1, $newDirection->getDirection(), 'Ошибка вращения объекта');
        });

        $rotateCommand = new RotateCommand($rotatableMock);
        $rotateCommand->execute();
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRotateCommandUndefinedDirection()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirection')->willThrowException(new UnexpectedValueException);
        $rotatableMock->method('getAngularVelocity')->willReturn(7);

        $rotateCommand = new RotateCommand($rotatableMock);
        $this->expectException(UnexpectedValueException::class);
        $rotateCommand->execute();
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRotateCommandUndefinedAngularVelocity()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirection')->willReturn(new Direction(2, 8));
        $rotatableMock->method('getAngularVelocity')->willThrowException(new UnexpectedValueException);

        $rotateCommand = new RotateCommand($rotatableMock);
        $this->expectException(UnexpectedValueException::class);
        $rotateCommand->execute();
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRotateCommandCantSetDirection()
    {
        /** @var Rotatable&MockObject $rotatableMock */
        $rotatableMock = $this->createMock(Rotatable::class);
        $rotatableMock->method('getDirection')->willReturn(new Direction(2, 8));
        $rotatableMock->method('getAngularVelocity')->willReturn(7);
        $rotatableMock->method('setDirection')->willThrowException(new Exception);

        $rotateCommand = new RotateCommand($rotatableMock);
        $this->expectException(Exception::class);
        $rotateCommand->execute();
    }
}
