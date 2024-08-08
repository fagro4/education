<?php

use App\Exceptions\NotFoundException;
use App\Interfaces\SenderInterface;
use App\Interfaces\UObject;
use App\IoC\AdapterGenerateCommand;
use App\IoC\IoC;
use App\Move\Movable;
use App\Move\MoveCommand;
use App\Queue\Async\AwaitCommand;
use App\Queue\Async\CommandQueue;
use App\Queue\Receiver;
use App\Queue\Sender;
use App\Thread\Action\DefaultStrategy;
use App\Thread\StartThreadCommand;
use App\Thread\StopThreadCommand;
use App\Thread\Thread;

IoC::resolve(
    'IoC.Register',
    'Adapter',
    function (string $interface, UObject $target) {
        $refInterface = new ReflectionClass($interface);
        $className = $refInterface->getShortName() . "Adapter";

        if (!class_exists($className)) (new AdapterGenerateCommand($interface))->execute();

        return new ($className)($target);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'CommandQueue.Create',
    fn (...$attrs) => new CommandQueue($attrs[0], $attrs[1])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Receiver.Create',
    fn (...$attrs) => new Receiver($attrs[0])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Sender.Create',
    fn (...$attrs) => new Sender($attrs[0])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Thread.Create',
    fn (...$attrs) => new Thread($attrs[0], new DefaultStrategy)
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Thread.Start',
    fn (...$attrs) => new StartThreadCommand($attrs[0])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Thread.Stop',
    fn (...$attrs) => new StopThreadCommand($attrs[0])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Await',
    fn (...$attrs) => new AwaitCommand($attrs[0])
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Register',
    function (string $uid, SenderInterface $sender = null) {
        IoC::resolve(
            'IoC.Register',
            "Game.$uid",
            fn () => $sender
        )->execute();

        $gameObjects = [];
        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects",
            fn () => $gameObjects
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects.Register",
            function (string $uid, mixed $object) use (&$gameObjects) {
                $gameObjects[$uid] = $object;
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects.Get",
            function (string $uid) use (&$gameObjects) {
                return $gameObjects[$uid];
            }
        )->execute();
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Get',
    fn (string $uid) => IoC::resolve("Game.$uid")
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Factory.Get',
    function (string $commandCode, ...$attrs) {
        return match ($commandCode) {
            'move' => IoC::resolve('Command.Move', ...$attrs),
            default => throw new NotFoundException("Команда не определена"),
        };
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Move',
    function (Movable $object) {
        return new MoveCommand($object);
    }
)->execute();