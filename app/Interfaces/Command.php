<?php

namespace App\Interfaces;

interface Command
{
    public function execute(): void;
}