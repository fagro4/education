<?php

namespace App\Interfaces;

interface Order
{
    public function getActionCode(): string;
    public function setActionCode(string $code): self;
    public function getGameId(): string;
    public function setGameId(string $gameId): self;
    public function getObjectId(): string;
    public function setObjectId(string $objectId): self;
    public function getParams(): array;
    public function setParams(array $params): self;
}