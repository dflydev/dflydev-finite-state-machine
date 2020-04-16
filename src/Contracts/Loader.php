<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

interface Loader
{
    /**
     * @param mixed $resource
     */
    public function load($resource): void;

    /**
     * @param mixed $resource
     */
    public function supports($resource): bool;
}
