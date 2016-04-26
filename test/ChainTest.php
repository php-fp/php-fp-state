<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::chain'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testChain()
    {
        $this->assertEquals(
            State::of(2)
                ->chain(function () {
                    return State::of(3);
                })
                ->run('hello'),
            [3, 'hello'],
            'Chains.'
        );
    }
}
