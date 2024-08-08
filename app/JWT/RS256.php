<?php

namespace App\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Interfaces\JWTService;
use App\Interfaces\UserContextInterface;
use App\User\UserContext;

class RS256 implements JWTService
{
    public function __construct(
        private string $publicKey
    ) {
    }

    public function decode(string $jwt): UserContextInterface
    {
        $payload = JWT::decode($jwt, new Key($this->publicKey, 'RS256'));
        return new UserContext(
            $payload->name,
            $payload->login,
            $payload->session
        );
    }
}