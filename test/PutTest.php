<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class PutTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::put'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testModify()
    {
        $this->assertEquals(
            State::of(2)->chain(
                function ($x)
                {
                    return State::put(55)->map(
                        function () use ($x)
                        {
                            return $x;
                        }
                    );
                }
            )->run(5),
            [2, 55],
            'Mutates state.'
        );
    }
}
