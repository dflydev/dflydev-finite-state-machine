<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Guard;

use Dflydev\FiniteStateMachine\Contracts\GuardCallback;
use Dflydev\FiniteStateMachine\Contracts\GuardCallbackFactory;

class GuardCallbackResolver
{
    private array $guardCallbackFactories;

    public function __construct(GuardCallbackFactory ...$guardCallbackFactories)
    {
        $this->guardCallbackFactories = $guardCallbackFactories + static::defaultGuardCallbackFactories();
    }

    public function register(GuardCallbackFactory $guardCallbackFactory): void
    {
        $this->guardCallbackFactories[] = $guardCallbackFactory;
    }

    /**
     * @param mixed $do
     */
    public function resolve($do): GuardCallback
    {
        foreach ($this->guardCallbackFactories as $guardCallbackFactory) {
            if ($guardCallbackFactory->supports($do)) {
                return $guardCallbackFactory->build($do);
            }
        }

        throw new \RuntimeException('Could not resolve guard callback');
    }

    public static function defaultGuardCallbackFactories(): array
    {
        return [
            new CallableGuardCallbackFactory(),
        ];
    }
}
