<?php

namespace App\Field;

use App\Interfaces\Command;
use App\IoC\IoC;

class HandleHitInCellCommand implements Command
{
    public function __construct(
        private string $gameUid,
        private string $gridKey,
        private string $objectId
    ) {
    }

    public function execute(): void
    {
        $actualCellIndex = IoC::resolve('Game.Object.Cell.Get', $this->gameUid, $this->gridKey, $this->objectId);
        $previousCellIndex = IoC::resolve('Game.Object.Cell.Get', $this->gameUid, $this->gridKey, $this->objectId . "_last");

        if ($previousCellIndex === $actualCellIndex)
            return;

        if ($previousCellIndex !== null) {
            IoC::resolve(
                "Game.$this->gameUid.Storage.Grid.$this->gridKey.Objects.Remove",
                $previousCellIndex,
                $this->objectId
            );
        }

        if ($actualCellIndex !== null) {
            IoC::resolve(
                "Game.$this->gameUid.Storage.Grid.$this->gridKey.Objects.Append",
                $actualCellIndex,
                $this->objectId
            );
        }
    }
}