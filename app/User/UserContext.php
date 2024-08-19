<?php

namespace App\User;

use App\Interfaces\UserContextInterface;

class UserContext implements UserContextInterface
{
    public function __construct(
        private string $name,
        private string $login,
        private string $sessionId
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}