<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine;

use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;
use Dflydev\FiniteStateMachine\Graph\GraphResolver;
use Dflydev\FiniteStateMachine\ObjectProxy\ObjectProxyResolver;

class FiniteStateMachineFactory
{
    private GraphResolver $graphResolver;
    private ObjectProxyResolver $objectProxyResolver;
    private ?EventDispatcher $eventDispatcher;

    public function __construct(
        GraphResolver $graphResolver,
        ObjectProxyResolver $objectProxyResolver,
        ?EventDispatcher $eventDispatcher = null
    ) {
        $this->graphResolver = $graphResolver;
        $this->objectProxyResolver = $objectProxyResolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(object $object, string $graph = 'default'): FiniteStateMachine
    {
        $graph = $this->graphResolver->resolve($object, $graph);
        $objectProxy = $this->objectProxyResolver->resolve($object, $graph->objectProxyOptions());

        return new FiniteStateMachine($objectProxy, $graph, $this->eventDispatcher);
    }
}
