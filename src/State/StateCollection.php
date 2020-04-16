<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\State;

class StateCollection
{
    /**
     * @var array<int, State>
     */
    private array $states = [];

    /**
     * @var array<string, State>
     */
    private array $statesByName = [];

    public function __construct(State ...$states)
    {
        foreach ($states as $state) {
            $this->add($state);
        }
    }

    public function add(State $state): void
    {
        $this->states[] = $state;
        $this->statesByName[$state->name()] = $state;
    }

    public function named(string $name): State
    {
        if (! isset($this->statesByName[$name])) {
            throw new \RuntimeException(sprintf('No state named "%s"', $name));
        }

        return $this->statesByName[$name];
    }

    public function names(): array
    {
        return array_keys($this->statesByName);
    }
}
