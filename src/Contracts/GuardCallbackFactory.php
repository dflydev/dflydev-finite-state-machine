<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

interface GuardCallbackFactory
{
    /**
     * @param callable $do
     */
    public function build($do): GuardCallback;

    /**
     * @param callable $do
     */
    public function supports($do): bool;
}
