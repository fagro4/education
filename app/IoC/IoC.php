<?php

namespace App\IoC;

use App\Exceptions\NotFoundException;
use App\Interfaces\Container;
use Closure;

class IoC implements Container
{
    private static array $bindings;

    private static function getBindings(): array
    {
        if (empty(self::$bindings)) {
            self::$bindings = [
                'IoC.Register' => fn (string $key, Closure $cb, ...$args) => new RegisterCommand(
                    $key,
                    fn (string $key, object $obj) => self::$bindings[$key] = $obj,
                    $cb,
                    $args
                )
            ];
        }
        return self::$bindings;
    }

    public static function resolve(string $key, mixed ...$args): mixed
    {
        if (!isset(self::getBindings()[$key]))
            throw new NotFoundException("Зависимость $key не объявлена", 1);

        return self::getBindings()[$key](...$args);
    }
}