<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class RunTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::run'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testRun()
    {
        $this->assertEquals(
            State::of(5)->run(2),
            [5, 2],
            'Runs the computation.'
        );
    }
}
