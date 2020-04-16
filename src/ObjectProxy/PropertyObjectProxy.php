<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\ObjectProxy;

use Dflydev\FiniteStateMachine\Contracts\ObjectProxy;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;
use ReflectionProperty;

class PropertyObjectProxy implements ObjectProxy
{
    private object $object;
    private ReflectionProperty $property;

    public function __construct(object $object, ReflectionProperty $property)
    {
        $this->object = $object;
        $this->property = $property;
    }

    public function object(): object
    {
        return $this->object;
    }

    public function state(): ?string
    {
        return $this->property->getValue($this->object);
    }

    public function apply(
        Transition $transition,
        State $fromState,
        State $toState
    ): void {
        $this->property->setValue($this->object, $toState->name());
    }
}
