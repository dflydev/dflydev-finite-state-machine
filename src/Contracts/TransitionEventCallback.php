<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

interface TransitionEventCallback
{
    public function __invoke(
        string $when,
        object $object,
        Transition $transition,
        State $fromState,
        State $toState
    ): void;
}
