<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Event;

use Dflydev\FiniteStateMachine\Contracts\Event;

class Applied implements Event
{
    use EventBehavior;

    public function when(): string
    {
        return 'after';
    }
}
