<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Transition;

use Dflydev\FiniteStateMachine\State\State;

class Transition
{
    private string $name;
    private string $toStateName;
    private array $fromStateNames;
    private array $metadata;

    public function __construct(string $name, string $toStateName, array $fromStateNames, array $metadata = [])
    {
        $this->name = $name;
        $this->toStateName = $toStateName;
        $this->fromStateNames = $fromStateNames;
        $this->metadata = $metadata;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function toStateName(): string
    {
        return $this->toStateName;
    }

    public function canTransitionFrom(State $state): bool
    {
        foreach ($this->fromStateNames as $fromStateName) {
            if ($state->name() === $fromStateName) {
                return true;
            }
        }

        return false;
    }

    public function cannotTransitionFrom(State $state): bool
    {
        foreach ($this->fromStateNames as $fromStateName) {
            if ($state->name() === $fromStateName) {
                return false;
            }
        }

        return true;
    }
}
