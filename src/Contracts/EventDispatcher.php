<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Contracts;

interface EventDispatcher
{
    public function dispatch(Event $event): void;
}
