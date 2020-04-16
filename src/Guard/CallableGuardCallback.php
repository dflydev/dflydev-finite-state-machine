<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Guard;

use Dflydev\FiniteStateMachine\Contracts\GuardCallback;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

class CallableGuardCallback implements GuardCallback
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

    public function __invoke(object $object, Transition $transition, State $fromState, State $toState): bool
    {
        return ($this->callable)($object, $transition, $fromState, $toState);
    }
}
