<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\Graph\Graph;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\State\StateCollection;
use Dflydev\FiniteStateMachine\Transition\Transition;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;

interface ExposedFiniteStateMachine
{
    public function graph(): Graph;

    public function allStates(): StateCollection;

    public function state(string $name): State;

    public function allTransitions(): TransitionCollection;

    public function transition(string $name): Transition;
}
