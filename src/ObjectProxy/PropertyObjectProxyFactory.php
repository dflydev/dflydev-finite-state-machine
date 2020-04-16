<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\ObjectProxy;

use Dflydev\FiniteStateMachine\Contracts\ObjectProxy;
use Dflydev\FiniteStateMachine\Contracts\ObjectProxyFactory;
use ReflectionClass;
use ReflectionProperty;

class PropertyObjectProxyFactory implements ObjectProxyFactory
{
    private string $defaultPropertyName;

    public function __construct(string $defaultPropertyName = 'state')
    {
        $this->defaultPropertyName = $defaultPropertyName;
    }

    public function build(object $object, array $options): ObjectProxy
    {
        $property = new ReflectionProperty($object, $options['property_path'] ?? $this->defaultPropertyName);
        $property->setAccessible(true);

        return new PropertyObjectProxy(
            $object,
            $property
        );
    }

    public function supports(object $object, array $options): bool
    {
        $class = new ReflectionClass($object);

        return $class->hasProperty($options['property_path'] ?? $this->defaultPropertyName);
    }
}
