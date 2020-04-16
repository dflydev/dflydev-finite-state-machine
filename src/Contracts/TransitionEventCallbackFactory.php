<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

interface TransitionEventCallbackFactory
{
    /**
     * @param callable $do
     */
    public function build($do): TransitionEventCallback;

    /**
     * @param callable $do
     */
    public function supports($do): bool;
}
