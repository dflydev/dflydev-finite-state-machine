<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;

interface FiniteStateMachine
{
    public function can(string $transition): bool;

    public function apply(string $transition): void;

    public function currentState(): State;

    public function availableTransitions(): TransitionCollection;
}
