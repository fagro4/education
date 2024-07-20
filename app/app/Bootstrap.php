<?php

use App\Interfaces\UObject;
use App\IoC\AdapterGenerateCommand;
use App\IoC\IoC;

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
