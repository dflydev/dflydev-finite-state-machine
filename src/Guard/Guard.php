<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Guard;

use Dflydev\FiniteStateMachine\Contracts\GuardCallback;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

class Guard
{
    private string $name;
    private array $transitionNames;
    private array $fromStateNames;
    private array $toStateNames;
    private GuardCallback $callback;

    public function __construct(
        string $name,
        array $transitionNames,
        array $fromStateNames,
        array $toStateNames,
        GuardCallback $callback
    ) {
        $this->name = $name;
        $this->transitionNames = $transitionNames;
        $this->fromStateNames = $fromStateNames;
        $this->toStateNames = $toStateNames;
        $this->callback = $callback;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function cannot(object $object, Transition $transition, State $fromState, State $toState): bool
    {
        $matches = [];

        if (count($this->transitionNames) > 0) {
            $matches[] = in_array($transition->name(), $this->transitionNames);
        }

        if (count($this->fromStateNames) > 0) {
            $matches[] = in_array($fromState->name(), $this->fromStateNames);
        }

        if (count($this->toStateNames) > 0) {
            $matches[] = in_array($toState->name(), $this->toStateNames);
        }

        if (count($matches) === 0) {
            return false;
        }

        if (in_array(false, $matches)) {
            return false;
        }

        return !$this->callback->__invoke($object, $transition, $fromState, $toState);
    }
}
