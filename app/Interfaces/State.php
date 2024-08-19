<?php

namespace App\Interfaces;

interface State
{
    public function handle(): ?State;
}