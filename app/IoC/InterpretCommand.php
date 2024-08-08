<?php

namespace App\IoC;

use App\Exceptions\Command\NotFoundException as CommandNotFoundException;
use App\Exceptions\Game\Object\NotFoundException as ObjectNotFoundException;
use App\Exceptions\NotFoundException;
use App\Interfaces\Command;

class InterpretCommand implements Command
{

    private mixed $object;
    private Command $command;

    public function __construct(
        private string $commandCode,
        private string $gameId,
        private string $objectId,
        private array $args = []
    ) {
        $this->object = IoC::resolve(
            "Game.$this->gameId.Objects.Get",
            $this->objectId
        );

        if (empty($this->object)) {
            throw new ObjectNotFoundException("Объект не найден", 1);
        }

        /** @var Command $command */
        $this->command = IoC::resolve(
            "Command.Factory.Get",
            $this->commandCode,
            $this->object,
            ...$this->args
        );

        if (empty($this->command)) {
            throw new CommandNotFoundException("Команда не найдена", 1);
        }
    }

    public function execute(): void
    {
        IoC::resolve("Game.$this->gameId.Queue.Send", $this->command);
    }
}