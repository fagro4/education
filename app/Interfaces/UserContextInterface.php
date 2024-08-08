<?php

namespace App\Interfaces;

interface UserContextInterface
{
    public function getName(): string;
    public function getLogin(): string;
    public function getSessionId(): string;
}