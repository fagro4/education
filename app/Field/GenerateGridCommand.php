<?php

namespace App\Field;

use App\GameObject;
use App\Interfaces\Command;
use App\Interfaces\Cell as ICell;
use App\IoC\IoC;
use App\Vector;

class GenerateGridCommand implements Command
{
    public function __construct(
        private string $gameUid,
        private string $key,
        private int $offsetX = 0,
        private int $offsetY = 0
    ) {
    }

    public function execute(): void
    {
        list($fieldWidth, $fieldHeight) = IoC::resolve('Game.Field.Size');
        list($cellWidth, $cellHeight) = IoC::resolve('Game.Field.Cell.Size');

        $x = 0 + $this->offsetX;
        $y = 0 + $this->offsetY;

        $cells = [];
        $cellObjects = [];
        $cellCommands = [];

        $i = 0;
        while (true) {
            if ($x >= $fieldWidth) {
                $y += $cellHeight;
                $x = 0;
            };

            if ($y >= $fieldHeight)
                break;

            $cell = IoC::resolve('Adapter', ICell::class, new GameObject);
            $cell->setWidth($cellWidth);
            $cell->setHeight($cellHeight);
            $cell->setPosition(new Vector($x + $cellWidth / 2, $y + $cellHeight / 2));

            $cells[$i] = $cell;
            $cellObjects[$i] = [];

            IoC::resolve(
                'IoC.Register',
                "Game.Storage.Grid." . $this->key . "." . $i,
                fn () => $cell
            )->execute();

            $x += $cellWidth;
            $i += 1;
        }

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key",
            fn () => $cells
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key.Objects.Get",
            function (int $index) use (&$cellObjects) {
                return $cellObjects[$index];
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key.Objects.Append",
            function (int $index, string $objectUid) use (&$cellObjects) {
                $cellObjects[$index][] = $objectUid;
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key.Objects.Remove",
            function (int $index, string $objectUid) use (&$cellObjects) {
                $cellObjects[$index] = array_filter($cellObjects[$index], fn ($item) => $item !== $objectUid);
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key.Command.Get",
            function (int $index) use (&$cellCommands) {
                return $cellCommands[$index];
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$this->gameUid.Storage.Grid.$this->key.Command.Set",
            function (int $index, Command $command) use (&$cellCommands) {
                $cellCommands[$index] = $command;
            }
        )->execute();
    }
}