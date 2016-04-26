<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class GetTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::get'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            0,
            'Takes no parameters.'
        );
    }

    public function testGet()
    {
        $this->assertEquals(
            State::of(2)->chain(
                function ($x)
                {
                    return State::get()->map(
                        function ($y) use ($x)
                        {
                            return $x + $y;
                        }
                    );
                }
            )->evalState(3),
            5,
            'Accesses state.'
        );
    }
}
