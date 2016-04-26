<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class ApTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::ap'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testMap()
    {
        $inc = function ($x)
        {
            return $x + 1;
        };

        $this->assertEquals(
            State::of($inc)
                ->ap(State::of(1))
                ->run(null),
            [2, null],
            'Aps.'
        );
    }
}
