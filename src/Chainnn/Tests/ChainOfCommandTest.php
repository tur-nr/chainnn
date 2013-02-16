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

use Chainnn\ChainOfCommand;

/**
 * Chain Of Command Tests
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class ChainOfCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create chain of command from map.
     */
    public function testCreateFromMap()
    {
        $chainOfCommand = ChainOfCommand::createFromArrayMap(array(
            'methodOne' => array(
                'childOne'
            ),
            'methodTwo',
            'methodThree'
        ));

        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('methodOne'));
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('methodTwo'));
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('methodThree'));

        $this->assertNull($chainOfCommand->findNextAvailableCommand('childOne'));
        $chainOfCommand->setCurrentCommand($chainOfCommand->findNextAvailableCommand('methodOne'));
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('childOne'));

        $this->assertNull($chainOfCommand->findNextAvailableCommand('methodNull'));
    }

    /**
     * Chain of command usage tests.
     */
    public function testChainOfCommand()
    {
        $chainOfCommand = ChainOfCommand::createFromArrayMap(array(
            'methodOne' => array(
                'childOne' => array(
                    'subOne' => array(
                        'subSubOne'
                    )
                )
            ),
            'methodTwo' => array(
                'childTwo' => array(
                    'subTwo' => array(
                        'subSubTwo'
                    )
                )
            )
        ));

        // method one
        $methodOne = $chainOfCommand->findNextAvailableCommand('methodOne');
        $this->assertNotNull($methodOne);

        $chainOfCommand->setCurrentCommand($methodOne);
        $childOne = $chainOfCommand->findNextAvailableCommand('childOne');
        $this->assertNotNull($childOne);

        $chainOfCommand->setCurrentCommand($childOne);
        $subOne = $chainOfCommand->findNextAvailableCommand('subOne');
        $this->assertNotNull($subOne);

        $chainOfCommand->setCurrentCommand($subOne);
        $subSubOne = $chainOfCommand->findNextAvailableCommand('subSubOne');
        $this->assertNotNull($subSubOne);

        // method two
        $methodTwo = $chainOfCommand->findNextAvailableCommand('methodTwo');
        $this->assertNotNull($methodTwo);
        $chainOfCommand->setCurrentCommand($methodTwo);

        $childTwo = $chainOfCommand->findNextAvailableCommand('childTwo');
        $this->assertNotNull($childTwo);
        $chainOfCommand->setCurrentCommand($childTwo);

        $subTwo = $chainOfCommand->findNextAvailableCommand('subTwo');
        $this->assertNotNull($subTwo);
        $chainOfCommand->setCurrentCommand($subTwo);

        $subSubTwo = $chainOfCommand->findNextAvailableCommand('subSubTwo');
        $this->assertNotNull($subSubTwo);
        $chainOfCommand->setCurrentCommand($subSubTwo);

        // try accessing another base commands child
        $this->assertNull($chainOfCommand->findNextAvailableCommand('childOne'));
        $this->assertNull($chainOfCommand->findNextAvailableCommand('subOne'));
        $this->assertNull($chainOfCommand->findNextAvailableCommand('subSubOne'));

        // try bubbling up
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('methodTwo'));
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('childTwo'));
        $this->assertNotNull($chainOfCommand->findNextAvailableCommand('subTwo'));
    }
}