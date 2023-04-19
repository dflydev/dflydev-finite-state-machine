<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Event;

use Dflydev\FiniteStateMachine\Contracts\Event;
use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;

class NullEventDispatcher implements EventDispatcher
{
    public function dispatch(Event $event): void
    {
    }
}
