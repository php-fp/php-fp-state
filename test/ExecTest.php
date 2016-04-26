<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class ExecTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::exec'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testExec()
    {
        $this->assertEquals(
            State::of(null)->run(2),
            [null, 2],
            'Executes the computation.'
        );
    }
}
