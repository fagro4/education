<?php

namespace App\Interpreter;

use App\Exceptions\PermissionDenied;
use App\Interfaces\Command;
use App\Interfaces\Order;
use App\Interfaces\UObject;
use App\Interfaces\UserContextInterface;
use App\IoC\IoC;

class InterpretateCommand implements Command
{
    public function __construct(private Order $order, private UserContextInterface $userContext)
    {
    }

    public function execute(): void
    {
        /** @var mixed $object */
        $object = (new ObjectInterpreter(
            $this->order->getObjectId(),
            $this->order->getGameId()
        ))->interpret();

        $acionAgreed = IoC::resolve(
            "Game." . $this->order->getGameId() . ".Objects.CheckAccess",
            $this->order->getObjectId(),
            $this->userContext
        );

        if (!$acionAgreed) {
            throw new PermissionDenied;
        }

        (new ActionInterpreter(
            $this->order->getActionCode(),
            $object,
            $this->order->getParams()
        ))->interpret();
    }
}