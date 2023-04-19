<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine;

use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;
use Dflydev\FiniteStateMachine\Contracts\ExposedFiniteStateMachine;
use Dflydev\FiniteStateMachine\Contracts\FiniteStateMachine as FiniteStateMachineContract;
use Dflydev\FiniteStateMachine\Contracts\ObjectProxy;
use Dflydev\FiniteStateMachine\Contracts\TransitionQueryableFiniteStateMachine;
use Dflydev\FiniteStateMachine\Event\Applied;
use Dflydev\FiniteStateMachine\Event\NullEventDispatcher;
use Dflydev\FiniteStateMachine\Event\Started;
use Dflydev\FiniteStateMachine\Event\TransitionEventDispatcher;
use Dflydev\FiniteStateMachine\Graph\Graph;
use Dflydev\FiniteStateMachine\Guard\GuardCollection;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\State\StateCollection;
use Dflydev\FiniteStateMachine\Transition\Transition;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;

class FiniteStateMachine implements FiniteStateMachineContract, ExposedFiniteStateMachine, TransitionQueryableFiniteStateMachine
{
    private Graph $graph;
    private ObjectProxy $objectProxy;
    private StateCollection $stateCollection;
    private TransitionCollection $transitionCollection;
    private GuardCollection $guardCollection;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        ObjectProxy $objectProxy,
        Graph $graph,
        ?EventDispatcher $eventDispatcher = null
    ) {
        $this->graph = $graph;
        $this->objectProxy = $objectProxy;
        $this->stateCollection = $graph->stateCollection();
        $this->transitionCollection = $graph->transitionCollection();
        $this->guardCollection = $graph->guardCollection();
        $this->eventDispatcher = new TransitionEventDispatcher(
            $eventDispatcher ?? new NullEventDispatcher(),
            $graph->transitionEventCollection()
        );
    }

    public function graph(): Graph
    {
        return $this->graph;
    }

    public function can(string $transition): bool
    {
        $resolvedTransition = $this->resolveTransition($transition);

        $currentState = $this->currentState();

        if ($resolvedTransition->cannotTransitionFrom($currentState)) {
            return false;
        }

        $toState = $this->stateCollection->named($resolvedTransition->toStateName());

        if ($this->guardCollection->cannot($this->objectProxy->object(), $resolvedTransition, $currentState, $toState)) {
            return false;
        }

        return true;
    }

    public function apply(string $transition): void
    {
        $resolvedTransition = $this->resolveTransition($transition);

        $currentState = $this->currentState();

        if ($resolvedTransition->cannotTransitionFrom($currentState)) {
            throw new \RuntimeException(sprintf('Cannot apply transition "%s"', $resolvedTransition->name()));
        }

        $toState = $this->stateCollection->named($resolvedTransition->toStateName());

        if ($this->guardCollection->cannot($this->objectProxy->object(), $resolvedTransition, $currentState, $toState)) {
            throw new \RuntimeException(sprintf('Cannot apply transition "%s"', $resolvedTransition->name()));
        }

        $this->eventDispatcher->dispatch(new Started(
            $this->graph,
            $this->objectProxy->object(),
            $resolvedTransition,
            $currentState,
            $toState
        ));

        $this->objectProxy->apply(
            $resolvedTransition,
            $currentState,
            $toState
        );

        $this->eventDispatcher->dispatch(new Applied(
            $this->graph,
            $this->objectProxy->object(),
            $resolvedTransition,
            $currentState,
            $toState
        ));
    }

    public function currentState(): State
    {
        $currentState = $this->objectProxy->state();

        if (is_null($currentState)) {
            throw new \RuntimeException('No state currently set');
        }

        return $this->stateCollection->named($currentState);
    }

    public function allStates(): StateCollection
    {
        return $this->stateCollection;
    }

    public function state(string $name): State
    {
        return $this->stateCollection->named($name);
    }

    public function allTransitions(): TransitionCollection
    {
        return $this->transitionCollection;
    }

    public function transition(string $name): Transition
    {
        return $this->transitionCollection->named($name);
    }

    public function availableTransitions(): TransitionCollection
    {
        return $this->transitionCollection->fromState($this->currentState());
    }

    /**
     * @param Transition|string $transitionOrTransitionName
     */
    private function resolveTransition($transitionOrTransitionName): Transition
    {
        if ($transitionOrTransitionName instanceof Transition) {
            return $transitionOrTransitionName;
        }

        return $this->transitionCollection->named($transitionOrTransitionName);
    }

    public function queryTransitions(callable $transitionVisitor): TransitionCollection
    {
        return new TransitionCollection(...array_filter($this->transitionCollection->toArray(), $transitionVisitor));
    }
}
