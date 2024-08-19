<?php

namespace App\Interfaces;

interface ReceiverInterface
{
    public function receive(): ?Command;
    public function isEmpty(): bool;
}