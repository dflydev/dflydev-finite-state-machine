<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\ObjectProxy;

use Dflydev\FiniteStateMachine\Contracts\ObjectProxy;
use Dflydev\FiniteStateMachine\Contracts\ObjectProxyFactory;

class ObjectProxyResolver
{
    /**
     * @var ObjectProxyFactory[]
     */
    private array $objectProxyFactories;

    public function __construct(ObjectProxyFactory ...$objectProxyFactories)
    {
        $this->objectProxyFactories = $objectProxyFactories;
    }

    public function add(ObjectProxyFactory $objectProxyFactory): void
    {
        $this->objectProxyFactories[] = $objectProxyFactory;
    }

    public function resolve(object $object, array $options = []): ObjectProxy
    {
        foreach ($this->objectProxyFactories as $objectProxyFactory) {
            if ($objectProxyFactory->supports($object, $options)) {
                return $objectProxyFactory->build($object, $options);
            }
        }

        throw new \RuntimeException(sprintf('No object proxy found for "%s"', get_class($object)));
    }
}
