<?php

namespace App\IoC;

use App\Interfaces\Command;
use App\Interfaces\UObject;
use ReflectionClass;

class AdapterGenerateCommand implements Command
{
    private ReflectionClass $reflection;
    private string $className;
    private string $interfaceName;
    private string $classDefinition;

    public function __construct(
        private string $interface
    ) {
        $this->reflection = new ReflectionClass($this->interface);
        $this->className = $this->reflection->getShortName() . "Adapter";
        $this->interfaceName = $this->reflection->getShortName();
    }

    public function execute(): void
    {
        $methodsDefinition =  $this->getMethodsDefinition();

        $this->classDefinition = "
            use App\IoC\IoC;
            class $this->className implements $this->interface {
                public function __construct(private \App\Interfaces\UObject \$target) {}
                $methodsDefinition
            }
        ";

        eval($this->classDefinition);
    }

    private function getMethodsDefinition(): string
    {
        $methodsDefinition = '';
        foreach ($this->reflection->getMethods() as $method) {
            $methodName = $method->getShortName();

            if (str_starts_with($methodName, 'get')) {
                $propertyName = str_replace('get', '', $methodName);
                $methodReturnType = $method->getReturnType();
                $key = "Adapter.$this->interfaceName.$propertyName.Get";

                IoC::resolve(
                    'IoC.Register',
                    $key,
                    function (UObject $target) use ($propertyName) {
                        return $target->getProperty($propertyName);
                    }
                )->execute();

                $methodsDefinition .= "
                    public function $methodName(): $methodReturnType {
                        return IoC::resolve(
                            'Adapter.$this->interfaceName.$propertyName.Get',
                            \$this->target
                        );
                    }
                ";
            }

            if (str_starts_with($methodName, 'set')) {
                $propertyName = str_replace('set', '', $methodName);
                $param = $method->getParameters()[0];
                $paramType = $param->getType()->getName();
                $key = "Adapter.$this->interfaceName.$propertyName.Set";

                IoC::resolve(
                    'IoC.Register',
                    $key,
                    function (UObject $target, mixed $value) use ($propertyName) {
                        return $target->setProperty($propertyName, $value);
                    }
                )->execute();

                $methodsDefinition .= "
                    public function $methodName($paramType \$$propertyName): $this->interface {
                        IoC::resolve(
                            '$key',
                            \$this->target,
                            \$$propertyName
                        );
                        return \$this;
                    }
                ";
            }
        }

        return $methodsDefinition;
    }
}