<?php

/**
 * This file is part of Chainnn.
 *
 * (c) Christopher Turner <turner296@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chainnn\Tests;

use Chainnn\Chain;

/**
 * Chain Tests
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the chain.
     *
     * @dataProvider providerChain
     *
     * @param \Chainnn\Chain $chain
     */
    public function testChain(Chain $chain)
    {
        $chain
            ->add(10)
            ->sub(4)
            ->mul(8)
            ->div(3)
        ;

        $this->assertEquals(16, $chain->eq());
        $this->assertNotEquals($chain, $chain->eq());
    }

    /**
     * Test chain provider.
     *
     * @return array
     */
    public function providerChain()
    {
        return array(
            array(
                new Chain(new \Calculator(), array(
                    'add', 'sub', 'mul', 'div', '^clear', '$eq'
                ))
            ),
            array(
                new Chain(new \ChainableCalculator())
            )
        );
    }
}