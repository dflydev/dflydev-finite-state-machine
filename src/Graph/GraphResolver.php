<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Graph;

class GraphResolver
{
    private GraphCollection $graphCollection;

    public function __construct()
    {
        $this->graphCollection = new GraphCollection();
    }

    public function register(Graph $graph): void
    {
        $this->graphCollection->add($graph);
    }

    public function resolve(object $object, string $graph = 'default'): Graph
    {
        return $this->graphCollection->for($object, $graph);
    }
}
