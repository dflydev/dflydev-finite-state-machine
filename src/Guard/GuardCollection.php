<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Guard;

use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Transition\Transition;

class GuardCollection
{
    /**
     * @var array<int, Guard>
     */
    private array $guards = [];

    /**
     * @var array<string, Guard>
     */
    private array $guardsByName = [];

    public function __construct(Guard ...$guards)
    {
        foreach ($guards as $guard) {
            $this->add($guard);
        }
    }

    public function add(Guard $guard): void
    {
        $this->guards[] = $guard;
        $this->guardsByName[$guard->name()] = $guard;
    }

    public function cannot(object $object, Transition $transition, State $fromState, State $toState): bool
    {
        foreach ($this->guards as $guard) {
            if ($guard->cannot($object, $transition, $fromState, $toState)) {
                return true;
            }
        }

        return false;
    }
}
