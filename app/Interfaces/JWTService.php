<?php

namespace App\Interfaces;

use stdClass;

interface JWTService
{
    public function decode(string $jwt): UserContextInterface;
}