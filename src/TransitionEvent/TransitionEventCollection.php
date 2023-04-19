<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\TransitionEvent;

use Dflydev\FiniteStateMachine\Contracts\Event;

class TransitionEventCollection
{
    /**
     * @var array <int, TransitionEvent>
     */
    private array $transitionEvents = [];

    /**
     * @var array <string, array<string, TransitionEvent>>
     */
    private array $transitionEventsByWhenAndName = [];

    public function __construct(TransitionEvent ...$transitionEvents)
    {
        foreach ($transitionEvents as $transitionEvent) {
            $this->add($transitionEvent);
        }
    }

    public function add(TransitionEvent $transitionEvent): void
    {
        $this->transitionEvents[] = $transitionEvent;

        if (!array_key_exists($transitionEvent->when(), $this->transitionEventsByWhenAndName)) {
            $this->transitionEventsByWhenAndName[$transitionEvent->when()] = [];
        }

        $this->transitionEventsByWhenAndName[$transitionEvent->when()][$transitionEvent->name()] = $transitionEvent;
    }

    public function fireIfMatches(Event $event): void
    {
        if (!isset($this->transitionEventsByWhenAndName[$event->when()])) {
            return;
        }

        /** @var TransitionEvent[] $transitionEvents */
        $transitionEvents = $this->transitionEventsByWhenAndName[$event->when()];

        foreach ($transitionEvents as $transitionEvent) {
            $transitionEvent->fireIfMatches(
                $event->when(),
                $event->object(),
                $event->transition(),
                $event->fromState(),
                $event->toState()
            );
        }
    }
}
