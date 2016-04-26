# The State Monad for PHP. [![Build Status](https://travis-ci.org/php-fp/php-fp-state.svg?branch=master)](https://travis-ci.org/php-fp/php-fp-state)

## Intro

Purely functional programming has no state. State is, after all, impurity, and purity is what we're aiming to achieve. Well, let's examine state for a moment.

An instruction executed within a given state leads to an updated state. If we were writing an interpreter, we might write the execution function's type signature as `State -> Function -> State`. The old state and the instruction are combined to make a new state.

Now, if we consider the program to be a list of instructions, and the desired output to be a state, we get `State -> [Instruction] -> State`. We have an initial state, and we combine these instructions, one after another, with the state, finally giving the end state.

`exec :: (State -> Function -> State) -> State -> [Instruction] -> State`

Does this look familiar?

`reduce :: (b -> a -> b) -> b -> [a] -> b`

Well, hey: we can implement the notion of state using `reduce`, resulting in a purely functional flow with equivalent power. However, this doesn't mean we should write all our programs like this: there are lots of places where state is totally unnecessary, and we should try to keep them away from it.

What we'd really like to do is to have state for specific computations, and it just so happens that monads are perfect for this! Each chaining function should be restricted to one state interaction, which forces you to clarify your process.

## API

In the following type signatures, constructors and static functions are written as one would see in pure languages such as Haskell. The others contain a pipe, where the type before the pipe represents the type of the current State instance, and the type after the pipe represents the function.

### `of :: a -> State a b`

The State monad has an applicative functor for wrapping values within a State monad. The `evalState` function is the complement:

```php
<?php

use PhpFp\State\State;

assert(State::of('boo')->evalState(null) == 'boo');
```

### `get :: -> State a b`

Similarly to the Reader monad's `ask` method, `get` provides read-only access to the application state. Within a chain method, the `get` State can be mapped over to access the current application state.

```php
<?php

use PhpFp\State\State;

$state = State::of(2)->chain(
    function ($x)
    {
        return State::get()->map(
            function ($y) use ($x)
            {
                return $x + $y;
            }
        );
    }
);

assert($state->evalState(6) == 8);
```

### `modify :: State a b | (b -> c) -> State a c`

Unlike the Reader monad, the computation state can be updated. There are two methods by which this can be achieved, the first one of which being the `modify` method. This method takes a function to map over the state, and can returns a State monad. Remember that this then needs to be mapped over to re-insert the value for the new State object.

```php
<?php

use PhpFp\State\State;

$state = State::of(2)->chain(
    function ($x)
    {
        $add = function ($x)
        {
            return $x + 1;
        };

        return State::modify($add)->map(
            function ($_) use ($x)
            {
                return $x;
            }
        );
    }
);

assert($state->run(-2) == [2, -1]);
```

### `put :: State a b | c -> State a c`

The `put` method is used to replace the State entirely, rather than transform it. Given a value, a State monad will be returned in which the state has been replaced. As with `modify`, the current State value will need to be added again.

```php
<?php

use PhpFp\State\State;

$state = State::of('hello')->chain(
    function ($x)
    {
        return State::put(5)->map(
            function ($_) use ($x)
            {
                return $x;
            }
        );
    }
);

assert($state->run('hello') == ['hello', 5]);
```

### `__construct :: (a -> (a, b)) -> State a b`

The regular constructor takes a unary function that returns a `[value, state]` pair. As with the others, this is the constructor you will probably use less frequently.

```php
<?php

use PhpFp\State\State;

$state = new State(
    function ($s)
    {
        return [2, $x];
    }
);

assert($state->evalState(null) == 2);
assert($state->evalState(5) == 5);
```

### `chain :: State a b | (a -> State c b) -> State c b`

The chain method will almost always be used with one of the three functions above, as these are the basic operations for state. However, you might sometimes want to do something more elaborate:

```php
<?php

use PhpFp\State\State;

$state = State::of(3)->chain(
    function ($x)
    {
        // Why? Who knows?
        return State::of($x + 1);
    }
);

assert($state->evalState(null) == 4);
```

### `map :: State a b | (a -> c) -> State c b`

The map function is what one would expect for a functor map - the inner value is transformed, and the state is left unaffected. Nothing special here:

```php
<?php

use PhpFp\State\State;

$inc = function ($x)
{
    return $x + 1;
}

assert(State::of(2)->map($inc)->evalState(null) == 3);
```

### `ap :: State (a -> c) b | State a b -> State c b`

This function can be used to apply a wrapped parameter to this monad's wrapped function. This implementation, much like `map`'s implementation, can be derived from the `chain` method:

```php
<?php

use PhpFp\State\State;

$inc = function ($x)
{
    return $x + 1;
}

assert(State::of($inc)->ap(State::of(2))->evalState(null) == 3);
```

### `evalState :: State a b | b -> a`

This method takes an initial state, runs the computation, and returns the resulting value (discarding the eventual state):

```php
<?php

use PhpFp\State\State;

assert(State::of(2)->evalState(3) == 2);
```

### `exec :: State a b | b -> b`

This method takes an initial state, runs the computation, and returns the final state (discarding the eventual value):

```php
<?php

use PhpFp\State\State;

assert(State::of(2)->exec(3) == 3);
```

### `run :: State a b | b -> (a, b)`

This method takes an initial state, runs the computation, and returns a pair of the final value and the final state:

```php
<?php

use PhpFp\State\State;

assert(State::of(2)->run(3) == [2, 3]);
```

## Contributing

I suspect it isn't impossible to tell the order in which these monads have been written by the standard of documentation, which shouldn't be the case. If there's anything that isn't clear, _please_ submit an issue or pull request - clarifications are more than welcome :)

As for code changes, the usual applies: the only changes I can foresee will be those that implement more idiosyncratic type classes, and they are wonderful and encouraged!
