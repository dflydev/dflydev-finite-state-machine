<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Graph;

use Dflydev\FiniteStateMachine\Guard\GuardCollection;
use Dflydev\FiniteStateMachine\State\StateCollection;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;
use Dflydev\FiniteStateMachine\TransitionEvent\TransitionEventCollection;

class Graph
{
    private string $className;
    private string $graph;
    private array $objectProxyOptions;
    private array $metadata;
    private StateCollection $stateCollection;
    private TransitionCollection $transitionCollection;
    private GuardCollection $guardCollection;
    private TransitionEventCollection $transitionEventCollection;

    public function __construct(
        string $className,
        string $graph,
        array $objectProxyOptions,
        array $metadata,
        StateCollection $stateCollection,
        TransitionCollection $transitionCollection,
        GuardCollection $guardCollection,
        TransitionEventCollection $transitionEventCollection
    ) {
        $this->className = $className;
        $this->graph = $graph;
        $this->objectProxyOptions = $objectProxyOptions;
        $this->metadata = $metadata;
        $this->stateCollection = $stateCollection;
        $this->transitionCollection = $transitionCollection;
        $this->guardCollection = $guardCollection;
        $this->transitionEventCollection = $transitionEventCollection;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function graph(): string
    {
        return $this->graph;
    }

    public function objectProxyOptions(): array
    {
        return $this->objectProxyOptions;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function stateCollection(): StateCollection
    {
        return $this->stateCollection;
    }

    public function transitionCollection(): TransitionCollection
    {
        return $this->transitionCollection;
    }

    public function guardCollection(): GuardCollection
    {
        return $this->guardCollection;
    }

    public function transitionEventCollection(): TransitionEventCollection
    {
        return $this->transitionEventCollection;
    }
}
