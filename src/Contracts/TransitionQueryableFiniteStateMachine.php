<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

use Dflydev\FiniteStateMachine\Transition\Transition;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;

interface TransitionQueryableFiniteStateMachine
{
    /**
     * @param callable(Transition):bool $transitionVisitor
     */
    public function queryTransitions(callable $transitionVisitor): TransitionCollection;
}
