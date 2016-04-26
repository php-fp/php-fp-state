<?php

namespace PhpFp\State;

/**
 * An OO implementation of the State monad.
 */
class State
{
    /**
     * The stateful computation to run.
     * @var callable
     */
    private $computation = null;

    /**
     * Applicative constructor for the State monad.
     * @param mixed $value The value to wrap.
     * @return State The wrapped value.
     */
    public static function of($value) : State
    {
        return new State(
            function ($state) use ($value) : array
            {
                return [$value, $state];
            }
        );
    }

    /**
     * Accessor wizardry for the data within the state.
     * @return State To be mapped over.
     */
    public static function get() : State
    {
        return new State(
            function ($state)
            {
                return [$state, $state];
            }
        );
    }

    /**
     * Mutator wizardry for the data within the state.
     * @param callable $f A function to run on the state.
     * @return State To be mapped over.
     */
    public static function modify(callable $f) : State
    {
        return new State(
            function ($state) use ($f) : array
            {
                return [null, $f($state)];
            }
        );
    }

    /**
     * Like State::modify, but it replaces the state entirely.
     * @param mixed $state The new computation state.
     * @return State To be mapped over.
     */
    public static function put($state) : State
    {
        return State::modify(
            function ($_) use ($state)
            {
                return $state;
            }
        );
    }

    /**
     * Class constructor. A unary function (for the state) is required.
     * @param callable $action
     */
    public function __construct(callable $action)
    {
        $this->computation = $action;
    }

    /**
     * PHP implementation of Haskell State's >>=.
     * @param callable $f State a c | a -> State b c -> State b c
     * @return State the newly-chained value.
     */
    public function chain(callable $f) : State
    {
        return new State(
            function ($state) use ($f) : array
            {
                list ($value, $newState) = $this->run($state);

                return $f($value)->run($newState);
            }
        );
    }

    /**
     * Standard functor mapping.
     * @param callable $f Transformer for the inner type.
     * @return State The new State-wrapped value.
     */
    public function map(callable $f) : State
    {
        return $this->chain(
            function ($x) use ($f)
            {
                return State::of($f($x));
            }
        );
    }

    /**
     * Application, derived from chain.
     * @param State $that The State-wrapped parameter.
     * @return State The State-wrapped value.
     */
    public function ap(State $that) : State
    {
        return $this->chain(
            function (callable $f) use ($that)
            {
                return $that->map($f);
            }
        );
    }

    /**
     * Evaluate the computation and return the new value.
     * @param mixed $state The State value to use.
     * @return mixed The returned value.
     */
    public function evalState($state)
    {
        return $this->run($state) [0];
    }

    /**
     * Evaluate the computation and return the final state.
     * @param mixed $state The beginning State.
     * @return mixed The final State.
     */
    public function exec($state)
    {
        return $this->run($state) [1];
    }

    /**
     * Evaluate the computation, return a [value, state] pair.
     * @param mixed $state The initial State.
     * @return array The final [value, state] pair.
     */
    public function run($state) : array
    {
        return call_user_func($this->computation, $state);
    }
}
