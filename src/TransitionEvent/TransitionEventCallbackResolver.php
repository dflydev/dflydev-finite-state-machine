<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\TransitionEvent;

use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallback;
use Dflydev\FiniteStateMachine\Contracts\TransitionEventCallbackFactory;

class TransitionEventCallbackResolver
{
    private array $transitionEventCallbackFactories;

    public function __construct(TransitionEventCallbackFactory ...$transitionEventCallbackFactories)
    {
        $this->transitionEventCallbackFactories = $transitionEventCallbackFactories + static::defaultTransitionEventCallbackFactories();
    }

    public function register(TransitionEventCallbackFactory $transitionEventCallbackFactory): void
    {
        $this->transitionEventCallbackFactories[] = $transitionEventCallbackFactory;
    }

    /**
     * @param mixed $do
     */
    public function resolve($do): TransitionEventCallback
    {
        foreach ($this->transitionEventCallbackFactories as $transitionEventCallbackFactory) {
            if ($transitionEventCallbackFactory->supports($do)) {
                return $transitionEventCallbackFactory->build($do);
            }
        }

        throw new \RuntimeException('Could not resolve transition event callback');
    }

    public static function defaultTransitionEventCallbackFactories(): array
    {
        return [
            new CallableTransitionEventCallbackFactory(),
        ];
    }
}
