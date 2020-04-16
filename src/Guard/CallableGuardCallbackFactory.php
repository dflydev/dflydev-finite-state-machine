<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Guard;

use Dflydev\FiniteStateMachine\Contracts\GuardCallback;
use Dflydev\FiniteStateMachine\Contracts\GuardCallbackFactory;

class CallableGuardCallbackFactory implements GuardCallbackFactory
{
    public function build($do): GuardCallback
    {
        return new CallableGuardCallback($do);
    }

    public function supports($do): bool
    {
        return is_callable($do);
    }
}
