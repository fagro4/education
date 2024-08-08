<?php

namespace App\Interfaces;

interface Controller
{
    public function handle(IncommingMessage $message): void;
}