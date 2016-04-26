<?php

namespace PhpFp\State\Test;

use PhpFp\State\State;

class ModifyTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\State\State::modify'))
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
                    $add = function ($x) {
                        return $x + 1;
                    };

                    return State::modify($add)->map(
                        function () use ($x)
                        {
                            return $x;
                        }
                    );
                }
            )->run(10),
            [2, 11],
            'Updates state.'
        );
    }
}
