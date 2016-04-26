<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class EvalStateTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::evalState'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testEvalState()
    {
        $this->assertEquals(
            State::of(2)->run(null),
            [2, null],
            'Evaluates with a state.'
        );
    }
}
