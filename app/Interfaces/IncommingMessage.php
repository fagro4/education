<?php

namespace App\Interfaces;

interface IncommingMessage
{
    public function getCommandCode(): string;
    public function getGameId(): string;
    public function getObjectId(): string;
    public function getParams(): array;
    public function getToken(): string;
}