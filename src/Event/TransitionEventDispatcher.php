<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Event;

use Dflydev\FiniteStateMachine\Contracts\Event;
use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;
use Dflydev\FiniteStateMachine\TransitionEvent\TransitionEventCollection;

class TransitionEventDispatcher implements EventDispatcher
{
    private EventDispatcher $eventDispatcher;
    private TransitionEventCollection $transitionEventCollection;

    public function __construct(EventDispatcher $eventDispatcher, TransitionEventCollection $transitionEventCollection)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->transitionEventCollection = $transitionEventCollection;
    }

    public function dispatch(Event $event): void
    {
        $this->transitionEventCollection->fireIfMatches($event);
        $this->eventDispatcher->dispatch($event);
    }
}
