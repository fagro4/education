<?php

namespace App\Field;

use App\Interfaces\Command;
use App\IoC\IoC;

class CompareObjectsCommand implements Command
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

        // Зафиксируем последнее состояние объекта в ячейке
        IoC::resolve(
            'Game.Object.Cell.Set',
            $this->gameUid,
            $this->gridKey,
            $this->objectId . "_last",
            $actualCellIndex
        );

        if ($actualCellIndex === null)
            return;

        $cellObjects = IoC::resolve(
            "Game." . $this->gameUid . ".Storage.Grid." . $this->gridKey . ".Objects.Get",
            $actualCellIndex
        );

        $siblings = array_filter($cellObjects, fn ($id) => $id !== $this->objectId);
        $checkCollisionCommands = array_map([$this, 'createCheckCollisionCommand'], $siblings);

        IoC::resolve(
            "Game.$this->gameUid.Storage.Grid.$this->gridKey.Command.Set",
            $previousCellIndex ?? $actualCellIndex,
            IoC::resolve('Command.Create.Macro', ...$checkCollisionCommands)
        );
    }

    private function createCheckCollisionCommand($targetObjectId): Command
    {
        $targetObject = IoC::resolve("Game.$this->gameUid.Objects.Get", $targetObjectId);
        $object = IoC::resolve("Game.$this->gameUid.Objects.Get", $this->objectId);

        return IoC::resolve('Command.Create.CheckCollision', $targetObject, $object);
    }
}