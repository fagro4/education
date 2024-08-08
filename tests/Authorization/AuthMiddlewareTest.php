<?php

require_once $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . 'app/Bootstrap.php';

use App\Exceptions\PermissionDenied;
use App\Interfaces\IncommingMessage;
use App\Interfaces\JWTService;
use App\Interfaces\UserContextInterface;
use App\Middlewares\AuthMiddleware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


final class AuthMiddlewareTest extends TestCase
{
    public function testDifferentGameSessions()
    {
        /** @var IncommingMessage&MockObject $message */
        $message = $this->createMock(IncommingMessage::class);
        $message->method('getGameId')->willReturn("123");


        /** @var UserContextInterface&MockObject $userContext */
        $userContext = $this->createMock(UserContextInterface::class);
        $userContext->method('getSessionId')->willReturn("000");

        /** @var JWTService&MockObject $jwtService */
        $jwtService = $this->createMock(JWTService::class);

        $authMiddleware = new AuthMiddleware($jwtService);
        $this->expectException(PermissionDenied::class);
        $authMiddleware->handle($message);
    }
}