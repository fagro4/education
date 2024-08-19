<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

use App\Log\LogExceptionCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LogExceptionCommandTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoggingIsTriggered()
    {
        $testMessageText = 'Hello World';
        $exception = new Exception($testMessageText);
        $_self = $this;

        /** @var LoggerInterface&MockObject $logger */
        $logger = $this->createStub(LoggerInterface::class);
        $logger->expects($this->once())->method('error');
        $logger->method('error')
            ->willReturnCallback(function (string $message, array $context) use ($_self, $testMessageText) {
                $_self->assertEquals($message, $testMessageText);
            });

        $command = new LogExceptionCommand($exception, $logger);
        $command->execute();
    }
}