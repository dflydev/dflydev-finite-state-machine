<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\TransitionEvent;

use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallback;
use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallbackFactory;

class CallableTransitionEventCallbackFactory implements TransitionEventCallbackFactory
{
    public function build($do): TransitionEventCallback
    {
        return new CallableTransitionEventCallback($do);
    }

    public function supports($do): bool
    {
        return is_callable($do);
    }
}
