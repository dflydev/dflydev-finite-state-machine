<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\State;

class State
{
    const INITIAL = 'initial';
    const NORMAL = 'normal';
    const FINAL = 'final';

    private string $type;
    private string $name;
    private array $metadata;

    private function __construct(string $type, string $name, array $metadata = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->metadata = $metadata;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public static function initialTyped(string $name, array $metadata): self
    {
        return new static(static::INITIAL, $name, $metadata);
    }

    public static function normalTyped(string $name, array $metadata): self
    {
        return new static(static::NORMAL, $name, $metadata);
    }

    public static function finalTyped(string $name, array $metadata): self
    {
        return new static(static::FINAL, $name, $metadata);
    }
}
