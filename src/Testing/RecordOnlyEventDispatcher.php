<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Testing;

use Dflydev\FiniteStateMachine\Contracts\Event;
use Dflydev\FiniteStateMachine\Contracts\EventDispatcher;

class RecordOnlyEventDispatcher implements EventDispatcher
{
    /**
     * @var Event[]
     */
    private array $history = [];

    public function dispatch(Event $event): void
    {
        $this->history[] = $event;
    }

    public function history(): array
    {
        return $this->history;
    }

    public function flush(): void
    {
        $this->history = [];
    }
}
