<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\Graph\Graph;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

interface Event
{
    public function when(): string;

    public function graph(): Graph;

    public function object(): object;

    public function transition(): Transition;

    public function fromState(): State;

    public function toState(): State;
}
