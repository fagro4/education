<?php

namespace App\Middlewares;

use App\Exceptions\PermissionDenied;
use App\Interfaces\IncommingMessage;
use App\Interfaces\JWTService;

class AuthMiddleware extends Chain
{
    public function __construct(private JWTService $jwtService)
    {
    }

    public function handle(IncommingMessage $message)
    {
        $token = $message->getToken();
        $userContext = $this->jwtService->decode($token);

        if ($userContext->getSessionId() !== $message->getGameId()) {
            throw new PermissionDenied('У пользователя нет права на отправку команд в игровую сессию');
        }

        return parent::handle($message);
    }
}