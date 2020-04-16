<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Transition;

use Dflydev\FiniteStateMachine\State\State;

class TransitionCollection
{
    /**
     * @var array<int, Transition>
     */
    private array $transitions = [];

    /**
     * @var array<string, Transition>
     */
    private array $transitionsByName = [];

    public function __construct(Transition ...$transitions)
    {
        foreach ($transitions as $transition) {
            $this->add($transition);
        }
    }

    public function add(Transition $transition): void
    {
        $this->transitions[] = $transition;
        $this->transitionsByName[$transition->name()] = $transition;
    }

    public function named(string $name): Transition
    {
        if (! isset($this->transitionsByName[$name])) {
            throw new \RuntimeException(sprintf('No transition named "%s"', $name));
        }

        return $this->transitionsByName[$name];
    }

    public function names(): array
    {
        return array_keys($this->transitionsByName);
    }

    public function fromState(State $state): self
    {
        /** @var Transition[] $transitions */
        $transitions = array_filter(
            $this->transitions,
            fn(Transition $transition) => $transition->canTransitionFrom($state)
        );

        return new static(...$transitions);
    }
}
