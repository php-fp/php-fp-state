<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class ConstructorTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionClass('PhpFp\State\State'))
            ->getConstructor()->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testConstructor()
    {
        $this->assertEquals(
            (new State(
                function ($s) {
                    return [2, $s];
                }
            ))->run(2),
            [2, 2],
            'Constructs.'
        );
    }
}
