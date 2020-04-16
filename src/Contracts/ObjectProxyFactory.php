<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

interface ObjectProxyFactory
{
    public function build(object $object, array $options): ObjectProxy;
    public function supports(object $object, array $options): bool;
}
