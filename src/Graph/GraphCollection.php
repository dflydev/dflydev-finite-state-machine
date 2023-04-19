<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Graph;

class GraphCollection
{
    /**
     * @var array<int, Graph>
     */
    private array $graphs = [];

    /**
     * @var array<string, array<string, Graph>>
     */
    private array $graphsByName = [];

    public function __construct(Graph ...$graphs)
    {
        foreach ($graphs as $graph) {
            $this->add($graph);
        }
    }

    public function add(Graph $graph): void
    {
        $this->graphs[] = $graph;

        if (!array_key_exists($graph->className(), $this->graphsByName)) {
            $this->graphsByName[$graph->className()] = [];
        }

        $this->graphsByName[$graph->className()][$graph->graph()] = $graph;
    }

    public function for(object $object, string $graph = 'default'): Graph
    {
        $className = get_class($object);

        foreach ($this->graphsByName as $classNameForGraph => $graphs) {
            if (!$object instanceof $classNameForGraph) {
                continue;
            }

            if (isset($graphs[$graph])) {
                return $graphs[$graph];
            }
        }

        throw new \RuntimeException(sprintf(
            'No graph named "%s" for class named "%s"',
            $graph,
            $className
        ));
    }
}
