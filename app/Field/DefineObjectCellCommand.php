<?php

namespace App\Field;

use App\Interfaces\Cell;
use App\Interfaces\Command;
use App\IoC\IoC;
use App\Move\Movable;

class DefineObjectCellCommand implements Command
{
    public function __construct(
        private string $gameUid,
        private string $gridKey,
        private string $objectUid
    ) {
    }

    public function execute(): void
    {
        $object = IoC::resolve("Game.$this->gameUid.Objects.Get", $this->objectUid);
        $cells = IoC::resolve("Game.$this->gameUid.Storage.Grid.$this->gridKey");

        /** @var Cell $cell */
        foreach ($cells as $cellIndex => $cell) {
            if ($this->checkEntryIntoCell($object, $cell)) {
                IoC::resolve('Game.Object.Cell.Set', $this->gameUid, $this->gridKey, $this->objectUid, $cellIndex);
                break;
            }
        }
    }

    private function checkEntryIntoCell(Movable $object, Cell $cell)
    {
        $objectPosition = $object->getPosition();
        $x = $objectPosition->getX() >= ($cell->getPosition()->getX() - $cell->getWidth() / 2) && $objectPosition->getX() < ($cell->getPosition()->getX() + $cell->getWidth() / 2);
        $y = $objectPosition->getY() >= ($cell->getPosition()->getY() - $cell->getHeight() / 2) && $objectPosition->getY() < ($cell->getPosition()->getY() + $cell->getHeight() / 2);
        return $x && $y;
    }
}