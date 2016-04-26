<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class OfTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::of'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testApplicativeConstructor()
    {
        $this->assertEquals(
            State::of(2)->evalState(null),
            2,
            'Constructs an applicative.'
        );
    }
}
