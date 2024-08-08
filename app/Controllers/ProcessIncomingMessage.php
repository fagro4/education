<?php

namespace App\Controllers;

use App\Exceptions\NotFoundException;
use App\Interfaces\Command;
use App\Interfaces\Controller;
use App\Interfaces\IncommingMessage;
use App\Interfaces\SenderInterface;
use App\IoC\IoC;

class ProcessIncomingMessage implements Controller
{
    public function handle(IncommingMessage $message): void
    {
        /** @var SenderInterface $gameSender */
        $gameSender = IoC::resolve(
            'Game.Get',
            $message->getGameId()
        );

        /** @var Command $command */
        $interpretCommand = IoC::resolve(
            "Command.Interpret",
            $message->getCommandCode(),
            $message->getGameId(),
            $message->getObjectId(),
            $message->getParams()
        );

        $gameSender->send($interpretCommand);
    }
}