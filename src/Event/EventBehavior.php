<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Event;

use Dflydev\FiniteStateMachine\Graph\Graph;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

trait EventBehavior
{
    private Graph $graph;
    private object $object;
    private Transition $transition;
    private State $fromState;
    private State $toState;

    public function __construct(Graph $graph, object $object, Transition $transition, State $fromState, State $toState)
    {
        $this->graph = $graph;
        $this->object = $object;
        $this->transition = $transition;
        $this->fromState = $fromState;
        $this->toState = $toState;
    }

    public function graph(): Graph
    {
        return $this->graph;
    }

    public function object(): object
    {
        return $this->object;
    }

    public function transition(): Transition
    {
        return $this->transition;
    }

    public function fromState(): State
    {
        return $this->fromState;
    }

    public function toState(): State
    {
        return $this->toState;
    }
}
