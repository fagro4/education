<?php

use App\Collision\CheckCollisionCommand;
use App\Exceptions\Command\NotFoundException as CommandNotFoundException;
use App\Field\CompareObjectsCommand;
use App\Field\DefineObjectCellCommand;
use App\Field\GenerateGridCommand;
use App\Field\HandleHitInCellCommand;
use App\Interfaces\Command;
use App\Interfaces\ReceiverInterface;
use App\Interfaces\SenderInterface;
use App\Interfaces\State;
use App\Interfaces\ThreadInterface;
use App\Interfaces\UObject;
use App\Interfaces\UserContextInterface;
use App\IoC\AdapterGenerateCommand;
use App\IoC\InterpretCommand;
use App\IoC\IoC;
use App\JWT\RS256;
use App\MacroCommand;
use App\Move\Movable;
use App\Move\MoveCommand;
use App\Queue\Async\AwaitCommand;
use App\Queue\Async\CommandQueue;
use App\Queue\Receiver;
use App\Queue\Sender;
use App\Thread\Action\DefaultStrategy;
use App\Thread\StartThreadCommand;
use App\Thread\State\DefaultState;
use App\Thread\State\MoveToState;
use App\Thread\State\Thread as StateThread;
use App\Thread\StopThreadCommand;
use App\Thread\Thread;
use App\Vector;
use Ds\Map;

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
    'Command.Interpret',
    fn (...$attrs) => new InterpretCommand(...$attrs)
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Register',
    function (string $uid, SenderInterface $sender = null, $players = []) {
        IoC::resolve(
            'IoC.Register',
            "Game.$uid",
            fn () => $sender
        )->execute();

        $gameObjects = [];
        $playerObjects = [];
        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects",
            fn () => $gameObjects
        )->execute();


        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Players",
            fn () => $players
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects.Register",
            function (string $uid, mixed $object, UserContextInterface $userContext = null) use (&$gameObjects, &$playerObjects) {
                $gameObjects[$uid] = $object;
                if ($userContext !== null) {
                    $playerObjects[$uid] = $userContext->getLogin();
                }
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects.CheckAccess",
            function (string $uid, UserContextInterface $userContext) use (&$playerObjects) {
                return $playerObjects[$uid] === $userContext->getLogin();
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Objects.Get",
            function (string $uid) use (&$gameObjects) {
                return $gameObjects[$uid];
            }
        )->execute();

        IoC::resolve(
            'IoC.Register',
            "Game.$uid.Queue.Send",
            function (Command $command) use ($sender) {
                $sender->send($command);
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
            default => throw new CommandNotFoundException("Команда не определена"),
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

IoC::resolve(
    'IoC.Register',
    'JWT',
    function () {
        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuzWHNM5f+amCjQztc5QT
fJfzCC5J4nuW+L/aOxZ4f8J3FrewM2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJ
hzkPYLae7bTVro3hok0zDITR8F6SJGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548t
u4czCuqU8BGVOlnp6IqBHhAswNMM78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vS
opcT51koWOgiTf3C7nJUoMWZHZI5HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTz
TTqo1SCSH2pooJl9O8at6kkRYsrZWwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/B
wQIDAQAB
-----END PUBLIC KEY-----
EOD;

        return new RS256($publicKey);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Field.Size',
    fn () => [1024, 768]
)->execute();


IoC::resolve(
    'IoC.Register',
    'Game.Field.Size',
    fn () => [1024, 768]
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Field.Cell.Size',
    fn () => [128, 128]
)->execute();

$gridKeys = [];

IoC::resolve(
    'IoC.Register',
    'Game.Grid.Generate',
    function (string $gameUid, string $key, int $offsetX = 0, int $offsetY = 0) use (&$gridKeys) {
        $gridKeys[] = $key;
        (new GenerateGridCommand($gameUid, $key, $offsetX, $offsetY))->execute();
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Grid.Keys',
    function () use (&$gridKeys) {
        return $gridKeys;
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Object.Cell.Get',
    function (string $gameUid,  string $gridKey, string $objectId) {
        try {
            return IoC::resolve("Game." . $gameUid . "." . $gridKey . '.' . $objectId . ".Cell");
        } catch (\Throwable $e) {
            return null;
        }
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Object.Cell.Set',
    function (string $gameUid, string $gridKey, string $objectId, ?int $cell) {
        IoC::resolve(
            'IoC.Register',
            "Game." . $gameUid . "." . $gridKey . '.' . $objectId . ".Cell",
            fn () => $cell
        )->execute();
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Game.Cell.Objects.Append',
    function (string $gameUid, string $gridKey, int $cell, string $objectId) {
        IoC::resolve(
            'IoC.Register',
            "Game." . $gameUid . "." . $gridKey . '.' . $objectId . ".Cell",
            fn () => $cell
        )->execute();
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Create.CheckCollision',
    function (Movable $targetObject, Movable $object) {
        return new CheckCollisionCommand($targetObject, $object);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Create.Macro',
    function (...$command) {
        return new MacroCommand(...$command);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Create.DefineObjectCell',
    function (...$attrs) {
        return new DefineObjectCellCommand(...$attrs);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Create.HandleHitInCell',
    function (...$attrs) {
        return new HandleHitInCellCommand(...$attrs);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Command.Create.CompareObjects',
    function (...$attrs) {
        return new CompareObjectsCommand(...$attrs);
    }
)->execute();

$threadState = new Map([]);

IoC::resolve(
    'IoC.Register',
    'Thread.State.Create',
    fn () => new StateThread
)->execute();

IoC::resolve(
    'IoC.Register',
    'Thread.State.Set',
    function (ThreadInterface $thread, State $state) use (&$threadState) {
        $threadState[$thread] = $state;
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Thread.State.Get',
    function (ThreadInterface $thread) use (&$threadState) {
        return $threadState[$thread];
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Thread.State.MoveTo',
    function (ThreadInterface $thread, ReceiverInterface $receiver) {
        return new MoveToState($thread, $receiver);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Thread.State.Default',
    function (ThreadInterface $thread, ReceiverInterface $receiver) {
        return new DefaultState($thread, $receiver);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'Action.Factory.Get',
    function (string $commandCode, ...$attrs) {
        return IoC::resolve($commandCode, ...$attrs);
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'StartMove',
    function (Movable $object, int $initialVelocity) {
        $object->setVelocity(new Vector($initialVelocity, 0));
        (new MoveCommand($object))->execute();
    }
)->execute();

IoC::resolve(
    'IoC.Register',
    'StopMove',
    function (Movable $object) {
        $object->setVelocity(new Vector(0, 0));
        (new MoveCommand($object))->execute();
    }
)->execute();