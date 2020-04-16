<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

interface GuardCallback
{
    public function __invoke(object $object, Transition $transition, State $fromState, State $toState): bool;
}
