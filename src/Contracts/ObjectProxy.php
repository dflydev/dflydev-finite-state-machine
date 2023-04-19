<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

interface ObjectProxy
{
    public function object(): object;

    public function state(): ?string;

    public function apply(Transition $transition, State $fromState, State $toState): void;
}
