<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Event;

use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;
use Dflydev\FiniteStateMachine\Contracts\Event;

class NullEventDispatcher implements EventDispatcher
{
    public function dispatch(Event $event): void
    {
    }
}
