<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\TransitionEvent;

use Closure;
use Dflydev\FiniteStateMachine\Contracts\Event;
use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallback;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

class CallableTransitionEventCallback implements TransitionEventCallback
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(
        string $when,
        object $object,
        Transition $transition,
        State $fromState,
        State $toState
    ): void {
        ($this->callable)($when, $object, $transition, $fromState, $toState);
    }
}
