<?php

declare(strict_types=1);

namespace Tests\Fixtures;

class SebdesignExample
{
    public string $state;
    public ?string $spy = null;

    public function __construct(string $state = 'new')
    {
        $this->state = $state;
    }
}
