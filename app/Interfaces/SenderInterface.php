<?php

namespace App\Interfaces;

interface SenderInterface
{
    public function send(Command $command): void;
}