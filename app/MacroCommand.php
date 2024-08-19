<?php

namespace App;

use App\Exceptions\CommandException;
use App\Interfaces\Command;

class MacroCommand implements Command
{
    /** @var Command[] $commands */
    private array $commands;

    /**
     * @param Command[] $commands
     * @return void
     */
    public function __construct(Command ...$commands)
    {
        $this->commands = $commands;
    }

    public function execute(): void
    {
        foreach ($this->commands as $command) {
            try {
                $command->execute();
            } catch (\Exception $e) {
                throw new CommandException($e->getMessage(), 0, $e);
            }
        }
    }
}