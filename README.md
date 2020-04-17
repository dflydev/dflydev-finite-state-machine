# Finite-State Machine

This library is yet another finite-state machine implementation.

![Build Status](https://github.com/dflydev/dflydev-finite-state-machine/workflows/Build%20Status/badge.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dflydev/dflydev-finite-state-machine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dflydev/dflydev-finite-state-machine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dflydev/dflydev-finite-state-machine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dflydev/dflydev-finite-state-machine/?branch=master)
[![Code Climate](https://codeclimate.com/github/dflydev/dflydev-finite-state-machine/badges/gpa.svg)](https://codeclimate.com/github/dflydev/dflydev-finite-state-machine)

## Installation

```bash
composer require dflydev/finite-state-machine
```

## Usage

Given the following definition for a domain class:

```php
class DomainObject
{
    public string $state;
    public ?string $spy = null;

    public function __construct(string $state = 'new')
    {
        $this->state = $state;
    }
}
```

Given the following state definition for the "graphA" graph of our domain object:

```php
$domainObjectGraphDefinition = [
   'class' => DomainObject::class,
   'graph' => 'graphA', // default is "default"
   'property_path' => 'state', // Configures `PropertyObjectProxy`
   'metadata' => [
       'title' => 'Graph A',
   ],
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
               'do' => function (
                   object $object,
                   Transition $transition,
                   State $fromState,
                   State $toState
               ) {
                   $object->spy = 'guard_on_approving_from_rejected';

                   // If a guard returns false, the transition will not happen
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
               'do' => function (
                   string $when,
                   object $object,
                   Transition $transition,
                   State $fromState,
                   State $toState
               ) {
                   Assert::equals($fromState->name(), $object->state);

                   $object->spy = $when . ' ask_for_changes from accepted';
               },
           ]
       ],

       // will be called after applying a transition
       'after' => [
           'spy-after-approve' => [
               'on' => 'ask_for_changes',
               'from' => 'accepted',
               'do' => function (
                   string $when,
                   object $object,
                   Transition $transition,
                   State $fromState,
                   State $toState
               ) {
                   Assert::equals($toState->name(), $object->state);
                   Assert::equals('before ask_for_changes from accepted', $object->spy);

                   $object->spy = $when . ' ask_for_changes from accepted';
               },
           ]
       ],
   ]        
];
```

```php
use Dflydev\FiniteStateMachine\FiniteStateMachineFactory;
use Dflydev\FiniteStateMachine\Graph\GraphResolver;
use Dflydev\FiniteStateMachine\Loader\WinzouArrayLoader;
use Dflydev\FiniteStateMachine\ObjectProxy\ObjectProxyResolver;
use Dflydev\FiniteStateMachine\ObjectProxy\PropertyObjectProxyFactory;

$graphResolver = new GraphResolver();
$objectProxyResolver = new ObjectProxyResolver();

// Add an object proxy that can directly read the state property from our objects
$objectProxyResolver->add(new PropertyObjectProxyFactory());

// Load a graph definition into our graph resolver
(new WinzouArrayLoader($graphResolver))->load($domainObjectGraphDefinition);

$finiteStateMachineFactory = new FiniteStateMachineFactory(
    $this->getGraphResolver(),
    $this->getObjectProxyResolver()
);

$finiteStateMachine = $finiteStateMachineFactory->build($object);

// "new"
$finiteStateMachine->currentState()->name();

// (bool) false
$finiteStateMachine->can('ask_for_changes');

// (bool) true
$finiteStateMachine->can('create');

$finiteStateMachine->apply('create');

// "pending_review"
$finiteStateMachine->currentState()->name();
```

## Graph Definitions and Loaders

A graph is a named collection of states, transitions, and callbacks. An object may have multiple graphs defined.

The `GraphResolver` is responsible for resolving the graph definition for a given object (and optionally a graph name).

A `Graph` can be created manually and added to a `GraphResolver`. A `Loader` can be used to load a `Graph` into a `GraphResolver` based on specific types of resources.

### winzou/state-machine

This library ships with `WinzouArrayLoader`, a `Loader` implementation that is loosely drop-in compatible with [winzou/state-machine](https://github.com/winzou/state-machine) array-based graph definitions.

### Custom

This library ships with a `Loader` contract. Implementing this interface allows for the creation of custom graph definitions.

## License

MIT, see [LICENSE](LICENSE).
