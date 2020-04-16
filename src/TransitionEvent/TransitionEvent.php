<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\TransitionEvent;

use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallback;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

class TransitionEvent
{
    private string $when;
    private string $name;
    private array $transitionNames;
    private array $fromStateNames;
    private array $toStateNames;
    private TransitionEventCallback $transitionEventCallback;

    public function __construct(
        string $when,
        string $name,
        array $transitionNames,
        array $fromStateNames,
        array $toStateNames,
        TransitionEventCallback $transitionEventCallback
    ) {
        $this->when = $when;
        $this->name = $name;
        $this->transitionNames = $transitionNames;
        $this->fromStateNames = $fromStateNames;
        $this->toStateNames = $toStateNames;
        $this->transitionEventCallback = $transitionEventCallback;
    }

    public function when(): string
    {
        return $this->when;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function transitionNames(): array
    {
        return $this->transitionNames;
    }

    public function fromStateNames(): array
    {
        return $this->fromStateNames;
    }

    public function toStateNames(): array
    {
        return $this->toStateNames;
    }

    public function transitionEventCallback(): TransitionEventCallback
    {
        return $this->transitionEventCallback;
    }

    public function fireIfMatches(
        string $when,
        object $object,
        Transition $transition,
        State $fromState,
        State $toState
    ): void {
        if ($when !== $this->when) {
            return;
        }

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
            return;
        }

        if (in_array(false, $matches)) {
            return;
        }

        $this->transitionEventCallback->__invoke($when, $object, $transition, $fromState, $toState);
    }
}
