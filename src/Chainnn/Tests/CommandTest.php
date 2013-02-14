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

use Chainnn\Command;

/**
 * Command Tests
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Command.php')));
    }

    /**
     * Create from map test data provider.
     *
     * @return array
     */
    public function providerCreateFromMap()
    {
        return array(
            array('exampleOne', array()),
            array('exampleTwo', array('childOne', 'childTwo')),
            array('exampleThree', array(
                'childThree' => array(
                    'subChildOne'
                )
            )),
            array('^exampleFour', array()),
            array('$exampleFive', array()),
        );
    }

    /**
     * Create from map tests.
     *
     * @dataProvider providerCreateFromMap
     */
    public function testCreateFromMap($method, array $children = array())
    {
        $matches = array();
        preg_match('/^(\^|\$)?(.*)$/', $method, $matches);

        $isBase = $matches[1] == '^';
        $isEnd  = $matches[1] == '$';
        $methodName = $matches[2];

        $command = Command::createFromMap($method, $children);

        $this->assertEquals($methodName, $command->getMethodName());
        $this->assertEquals($isBase, $command->willReturnToBase());
        $this->assertEquals($isEnd, $command->willEndChain());
        $this->assertEquals(count($children), count($command->getChildren()));
    }

    /**
     * Test command hierarchy (children and parents).
     */
    public function testCommandHierarchy()
    {
        $parentCommand = new Command('parentMethod');
        $childCommand = new Command('childMethod');

        $parentCommand->addChild($childCommand);

        $this->assertTrue($parentCommand->hasChildren());
        $this->assertEquals(1, count($parentCommand->getChildren()));
        $this->assertTrue($parentCommand->hasChild($childCommand));
        $this->assertFalse($parentCommand->hasParent());
        $this->assertEquals($childCommand, $parentCommand->getChild('childMethod'));
        $this->assertEquals($childCommand, $parentCommand->getChild($childCommand));

        $this->assertTrue($childCommand->hasParent());
        $this->assertTrue($childCommand->isParent($parentCommand));
        $this->assertFalse($childCommand->hasChildren());
    }
}