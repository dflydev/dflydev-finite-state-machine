<?php

declare(strict_types=1);

namespace Tests\Feature;

use Dflydev\FiniteStateMachine\Contracts\Event;
use Dflydev\FiniteStateMachine\FiniteStateMachineFactory;
use Dflydev\FiniteStateMachine\Graph\GraphResolver;
use Dflydev\FiniteStateMachine\Loader\WinzouArrayLoader;
use Dflydev\FiniteStateMachine\ObjectProxy\ObjectProxyResolver;
use Dflydev\FiniteStateMachine\ObjectProxy\PropertyObjectProxyFactory;
use Dflydev\FiniteStateMachine\State\State;
use Dflydev\FiniteStateMachine\Testing\RecordOnlyEventDispatcher;
use Dflydev\FiniteStateMachine\Transition\Transition;
use stdClass;
use Tests\Fixtures\SebdesignExample;
use Tests\TestCase;
use Throwable;

class FiniteStateMachineTest extends TestCase
{
    private GraphResolver $graphResolver;
    private ObjectProxyResolver $objectProxyResolver;
    private FiniteStateMachineFactory $finiteStateMachineFactory;
    private RecordOnlyEventDispatcher $eventDispatcher;

    public function getGraphResolver(): GraphResolver
    {
        if (! isset($this->graphResolver)) {
            $this->graphResolver = new GraphResolver();
        }

        return $this->graphResolver;
    }

    public function getObjectProxyResolver(): ObjectProxyResolver
    {
        if (! isset($this->objectProxyResolver)) {
            $this->objectProxyResolver = new ObjectProxyResolver();
        }

        return $this->objectProxyResolver;
    }

    public function getFiniteStateMachineFactory(): FiniteStateMachineFactory
    {
        if (! isset($this->finiteStateMachineFactory)) {
            $this->finiteStateMachineFactory = new FiniteStateMachineFactory(
                $this->getGraphResolver(),
                $this->getObjectProxyResolver(),
                $this->getEventDispatcher()
            );
        }

        return $this->finiteStateMachineFactory;
    }

    public function getEventDispatcher(): RecordOnlyEventDispatcher
    {
        if (! isset($this->eventDispatcher)) {
            $this->eventDispatcher = new RecordOnlyEventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /** @test */
    public function it_cannot_resolve_unknown_object()
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage(
            'No graph named "default" for class named "stdClass"'
        );

        $badApple = new stdClass();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($badApple);
    }

    /** @test */
    public function it_cannot_find_missing_graph()
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage(
            'No graph named "default" for class named "Tests\Fixtures\SebdesignExample"'
        );

        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample);
    }

    /** @test */
    public function it_gets_graph_metadata()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals(['title' => 'Graph A'], $finiteStateMachine->graph()->metadata());
    }

    /** @test */
    public function it_gets_all_state_names()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals([
            'new',
            'pending_review',
            'awaiting_changes',
            'accepted',
            'published',
            'rejected',
        ], $finiteStateMachine->allStates()->names());
    }

    /** @test */
    public function it_gets_all_transition_names()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals([
            'create',
            'ask_for_changes',
            'cancel_changes',
            'submit_changes',
            'approve',
            'publish',
        ], $finiteStateMachine->allTransitions()->names());
    }

    /** @test */
    public function it_gets_state_by_name()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('awaiting_changes', $finiteStateMachine->state('awaiting_changes')->name());
    }

    /** @test */
    public function it_gets_transition_by_name()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('publish', $finiteStateMachine->transition('publish')->name());
    }

    /** @test */
    public function it_is_new()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('new', $finiteStateMachine->currentState()->name());
        $this->assertEmpty($finiteStateMachine->currentState()->metadata());
        $this->assertTrue($finiteStateMachine->can('create'));
        $this->assertEquals(['create'], $finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_is_pending_review()
    {
        $sebdesignExample = new SebdesignExample('pending_review');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('pending_review', $finiteStateMachine->currentState()->name());
        $this->assertEquals(['title' => 'Pending Review'], $finiteStateMachine->currentState()->metadata());
        $this->assertTrue($finiteStateMachine->can('ask_for_changes'));
        $this->assertTrue($finiteStateMachine->can('approve'));
        $this->assertEquals(['ask_for_changes', 'approve'], $finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_is_awaiting_changes()
    {
        $sebdesignExample = new SebdesignExample('awaiting_changes');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('awaiting_changes', $finiteStateMachine->currentState()->name());
        $this->assertEmpty($finiteStateMachine->currentState()->metadata());
        $this->assertTrue($finiteStateMachine->can('cancel_changes'));
        $this->assertTrue($finiteStateMachine->can('submit_changes'));
        $this->assertEquals(['cancel_changes', 'submit_changes'], $finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_is_accepted()
    {
        $sebdesignExample = new SebdesignExample('accepted');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('accepted', $finiteStateMachine->currentState()->name());
        $this->assertEmpty($finiteStateMachine->currentState()->metadata());
        $this->assertTrue($finiteStateMachine->can('ask_for_changes'));
        $this->assertTrue($finiteStateMachine->can('publish'));
        $this->assertEquals(['ask_for_changes', 'publish'], $finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_is_published()
    {
        $sebdesignExample = new SebdesignExample('published');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('published', $finiteStateMachine->currentState()->name());
        $this->assertEmpty($finiteStateMachine->currentState()->metadata());
        $this->assertEmpty($finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_is_rejected()
    {
        $sebdesignExample = new SebdesignExample('rejected');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals('rejected', $finiteStateMachine->currentState()->name());
        $this->assertEmpty($finiteStateMachine->currentState()->metadata());
        $this->assertFalse($finiteStateMachine->can('approve'));
        $this->assertEquals('guard_on_approving_from_rejected', $sebdesignExample->spy);
        $this->assertEquals(['approve'], $finiteStateMachine->availableTransitions()->names());
    }

    /** @test */
    public function it_transitions_from_new_to_pending_review_via_create()
    {
        $sebdesignExample = new SebdesignExample();

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $finiteStateMachine->apply('create');

        $this->assertEquals('pending_review', $sebdesignExample->state);

        $this->assertEventsFired([
            ['graphA', $sebdesignExample, 'new', 'pending_review', 'create'],
            ['graphA', $sebdesignExample, 'new', 'pending_review', 'create'],
        ]);
    }

    protected function assertEventsFired(array $expectedEvents): void
    {
        $expectedEvents = array_map(function ($expectedEvent) {
            $expectedEvent[1] = spl_object_hash($expectedEvent[1]);
            return $expectedEvent;
        }, $expectedEvents);

        $actualEvents = array_map(function (Event $event) {
            return [
                $event->graph()->graph(),
                spl_object_hash($event->object()),
                $event->fromState()->name(),
                $event->toState()->name(),
                $event->transition()->name()
            ];
        }, $this->getEventDispatcher()->history());

        $this->assertEquals($expectedEvents, $actualEvents);
    }

    /** @test */
    public function it_transitions_from_pending_review_to_awaiting_changes_via_ask_for_changes()
    {
        $sebdesignExample = new SebdesignExample('pending_review');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals([
            'title' => 'Ask for changes'
        ], $finiteStateMachine->availableTransitions()->named('ask_for_changes')->metadata());

        $finiteStateMachine->apply('ask_for_changes');

        $this->assertEquals('awaiting_changes', $sebdesignExample->state);
        $this->assertNull($sebdesignExample->spy);
    }

    /** @test */
    public function it_transitions_from_accepted_to_awaiting_changes_via_ask_for_changes()
    {
        $sebdesignExample = new SebdesignExample('accepted');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->assertEquals([
            'title' => 'Ask for changes'
        ], $finiteStateMachine->availableTransitions()->named('ask_for_changes')->metadata());

        $finiteStateMachine->apply('ask_for_changes');

        $this->assertEquals('awaiting_changes', $sebdesignExample->state);
        $this->assertEquals('after ask_for_changes from accepted', $sebdesignExample->spy);
    }

    /** @test */
    public function it_transitions_from_awaiting_changes_to_pending_review_via_cancel_changes()
    {
        $sebdesignExample = new SebdesignExample('awaiting_changes');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $finiteStateMachine->apply('cancel_changes');

        $this->assertEquals('pending_review', $sebdesignExample->state);
    }

    /** @test */
    public function it_transitions_from_awaiting_changes_to_pending_review_via_submit_changes()
    {
        $sebdesignExample = new SebdesignExample('awaiting_changes');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $finiteStateMachine->apply('submit_changes');

        $this->assertEquals('pending_review', $sebdesignExample->state);
    }

    /** @test */
    public function it_transitions_from_pending_review_to_accepted_via_approve()
    {
        $sebdesignExample = new SebdesignExample('pending_review');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $finiteStateMachine->apply('approve');

        $this->assertEquals('accepted', $sebdesignExample->state);
    }

    /** @test */
    public function it_transitions_from_rejected_to_accepted_via_approve()
    {
        $sebdesignExample = new SebdesignExample('rejected');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $this->expectException(Throwable::class);

        $finiteStateMachine->apply('approve');
    }

    /** @test */
    public function it_transitions_from_accepted_to_published_via_approve()
    {
        $sebdesignExample = new SebdesignExample('accepted');

        $finiteStateMachine = $this->getDefaultFiniteStateMachineFactory()
            ->build($sebdesignExample, 'graphA');

        $finiteStateMachine->apply('publish');

        $this->assertEquals('published', $sebdesignExample->state);
    }

    public function getDefaultFiniteStateMachineFactory(): FiniteStateMachineFactory
    {
        (new WinzouArrayLoader($this->getGraphResolver()))->load(static::getSebdesignExampleConfiguration());

        $this->getObjectProxyResolver()->add(new PropertyObjectProxyFactory());

        return $this->getFiniteStateMachineFactory();
    }

    public static function getSebdesignExampleConfiguration(): array
    {
        return [
            // class of your domain object
            'class' => SebdesignExample::class,

            // name of the graph (default is "default")
            'graph' => 'graphA',

            // property of your object holding the actual state (default is "state")
            'property_path' => 'state',

            'metadata' => [
                'title' => 'Graph A',
            ],

            // list of all possible states
            'states' => [
                // a state as associative array
                ['name' => 'new'],
                // a state as associative array with metadata
                [
                    'name' => 'pending_review',
                    'metadata' => ['title' => 'Pending Review'],
                ],
                // states as string
                'awaiting_changes',
                'accepted',
                'published',
                'rejected',
            ],

            // list of all possible transitions
            'transitions' => [
                'create' => [
                    'from' => ['new'],
                    'to' => 'pending_review',
                ],
                'ask_for_changes' => [
                    'from' =>  ['pending_review', 'accepted'],
                    'to' => 'awaiting_changes',
                    'metadata' => ['title' => 'Ask for changes'],
                ],
                'cancel_changes' => [
                    'from' => ['awaiting_changes'],
                    'to' => 'pending_review',
                ],
                'submit_changes' => [
                    'from' => ['awaiting_changes'],
                    'to' =>  'pending_review',
                ],
                'approve' => [
                    'from' => ['pending_review', 'rejected'],
                    'to' =>  'accepted',
                ],
                'publish' => [
                    'from' => ['accepted'],
                    'to' =>  'published',
                ],
            ],

            // list of all callbacks
            'callbacks' => [
                // will be called when testing a transition
                'guard' => [
                    'guard_on_approving_from_rejected' => [
                        // call the callback on a specific transition
                        'on' => 'approve',
                        'from' => 'rejected',
                        // will call the method of this class
                        'do' => function (object $object, Transition $transition, State $fromState, State $toState) {
                            $object->spy = 'guard_on_approving_from_rejected';

                            return false;
                        },
                        // arguments for the callback
                        'args' => ['object'],
                    ],
                ],

                // will be called before applying a transition
                'before' => [
                    'spy-before-approve' => [
                        'on' => 'ask_for_changes',
                        'from' => 'accepted',
                        'do' => function (string $when, object $object, Transition $transition, State $fromState, State $toState) {
                            static::assertEquals($fromState->name(), $object->state);

                            $object->spy = $when . ' ask_for_changes from accepted';
                        },
                    ]
                ],

                // will be called after applying a transition
                'after' => [
                    'spy-after-approve' => [
                        'on' => 'ask_for_changes',
                        'from' => 'accepted',
                        'do' => function (string $when, object $object, Transition $transition, State $fromState, State $toState) {
                            static::assertEquals($toState->name(), $object->state);
                            static::assertEquals('before ask_for_changes from accepted', $object->spy);

                            $object->spy = $when . ' ask_for_changes from accepted';
                        },
                    ]
                ],
            ]
        ];
    }
}
