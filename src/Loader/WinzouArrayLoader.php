<?php

declare(strict_types=1);

namespace Dflydev\FiniteStateMachine\Loader;

use Dflydev\FiniteStateMachine\Contracts\Loader;
use Dflydev\FiniteStateMachine\Graph\Graph;
use Dflydev\FiniteStateMachine\Graph\GraphResolver;
use Dflydev\FiniteStateMachine\Guard\Guard;
use Dflydev\FiniteStateMachine\Guard\GuardCallbackResolver;
use Dflydev\FiniteStateMachine\Guard\GuardCollection;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\State\StateCollection;
use Dflydev\FiniteStateMachine\Transition\Transition;
use Dflydev\FiniteStateMachine\Transition\TransitionCollection;
use Dflydev\FiniteStateMachine\TransitionEvent\TransitionEvent;
use Dflydev\FiniteStateMachine\TransitionEvent\TransitionEventCallbackResolver;
use Dflydev\FiniteStateMachine\TransitionEvent\TransitionEventCollection;

class WinzouArrayLoader implements Loader
{
    private GraphResolver $graphResolver;
    private GuardCallbackResolver $guardCallbackResolver;
    private TransitionEventCallbackResolver $transitionEventCallbackResolver;

    public function __construct(
        GraphResolver $graphResolver,
        ?GuardCallbackResolver $guardCallbackResolver = null,
        ?TransitionEventCallbackResolver $transitionEventCallbackResolver = null
    ) {
        $this->graphResolver = $graphResolver;
        $this->guardCallbackResolver = $guardCallbackResolver ?? new GuardCallbackResolver();
        $this->transitionEventCallbackResolver = $transitionEventCallbackResolver ?? new TransitionEventCallbackResolver();
    }

    public function load($resource): void
    {
        $stateCollection = new StateCollection();
        $transitionCollection = new TransitionCollection();
        $guardCollection = new GuardCollection();
        $transitionEventCollection = new TransitionEventCollection();

        foreach ($resource['transitions'] as $name => $transition) {
            $transitionCollection->add(new Transition(
                $name,
                $transition['to'],
                $transition['from'],
                $transition['metadata'] ?? []
            ));
        }

        foreach ($resource['states'] as $state) {
            $stateCollection->add(State::normalTyped(
                is_array($state) ? $state['name'] : $state,
                is_array($state) ? $state['metadata'] ?? [] : []
            ));
        }

        if (isset($resource['callbacks']['guard'])) {
            foreach ($resource['callbacks']['guard'] as $name => $setup) {
                $transitionNames = $setup['on'] ?? [];
                $fromStateNames = $setup['from'] ?? [];
                $toStateNames = $setup['to'] ?? [];
                $do = $setup['do'] ?? fn (): bool => false;

                $guardCollection->add(new Guard(
                    $name,
                    is_array($transitionNames) ? $transitionNames : [$transitionNames],
                    is_array($fromStateNames) ? $fromStateNames : [$fromStateNames],
                    is_array($toStateNames) ? $toStateNames : [$toStateNames],
                    $this->guardCallbackResolver->resolve($do)
                ));
            }
        }

        foreach (['before', 'after'] as $when) {
            if (isset($resource['callbacks'][$when])) {
                foreach ($resource['callbacks'][$when] as $name => $setup) {
                    $transitionNames = $setup['on'] ?? [];
                    $fromStateNames = $setup['from'] ?? [];
                    $toStateNames = $setup['to'] ?? [];
                    $do = $setup['do'] ?? function (): void {
                    };

                    $transitionEventCollection->add(new TransitionEvent(
                        $when,
                        $name,
                        is_array($transitionNames) ? $transitionNames : [$transitionNames],
                        is_array($fromStateNames) ? $fromStateNames : [$fromStateNames],
                        is_array($toStateNames) ? $toStateNames : [$toStateNames],
                        $this->transitionEventCallbackResolver->resolve($do)
                    ));
                }
            }
        }

        $graph = new Graph(
            $resource['class'],
            $resource['graph'] ?? 'default',
            $resource,
            $resource['metadata'] ?? [],
            $stateCollection,
            $transitionCollection,
            $guardCollection,
            $transitionEventCollection
        );

        $this->graphResolver->register($graph);
    }

    public function supports($resource): bool
    {
        return is_array($resource) && isset($resource['class'], $resource['states'], $resource['transitions']);
    }
}
