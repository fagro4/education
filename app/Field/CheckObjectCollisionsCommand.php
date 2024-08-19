<?php

namespace App\Field;

use App\Interfaces\Command;
use App\IoC\IoC;

class CheckObjectCollisionsCommand implements Command
{
    public function __construct(
        private string $gameUid,
        private string $objectId
    ) {
    }

    public function execute(): void
    {
        foreach (IoC::resolve('Game.Grid.Keys') as $gridKey) {
            $command = IoC::resolve(
                'Command.Create.Macro',
                IoC::resolve('Command.Create.DefineObjectCell', $this->gameUid, $gridKey, $this->objectId),
                IoC::resolve('Command.Create.HandleHitInCell', $this->gameUid, $gridKey, $this->objectId),
                IoC::resolve('Command.Create.CompareObjects', $this->gameUid, $gridKey, $this->objectId),
            );
            $command->execute();
        }
    }
}