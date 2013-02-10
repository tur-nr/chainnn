<?php

/**
 * This file is part of Chainnn.
 *
 * (c) Christopher Turner <turner296@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chainnn;

use Chainnn\Exception\LogicException;

/**
 * Command
 *
 * Commands are hierarchical references to methods of an object in a chain.
 *
 * @see \Chainnn\ChainOfCommand
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class Command
{
    /**
     * @var string $methodName
     */
    protected $methodName;

    /**
     * @var array $attributes
     */
    protected $attributes;

    /**
     * @var array $children
     */
    protected $children;

    /**
     * @var \Chainnn\Command $parent
     */
    protected $parent;

    /**
     * Creates a command instance from an array map representing the chain of
     * command. Will also iterate the creation of the command's children.
     *
     * Methods prefixed with a special character will have it's attributes set
     * as the following.
     *
     * $foo, endChain attribute will be set to true
     * ^foo, returnToBase attribute will be set to true
     *
     * N.b. Special characters are mutually exclusive.
     *
     * @param  string           $method
     * @param  array            $children
     * @return \Chainnn\Command
     */
    public static function createFromMap($method, array $children = array())
    {
        $flag = substr($method, 0, 1);
        $attr = array(
            'endChain' => $flag === '$',
            'returnToBase' => $flag === '^',
        );

        if (array_search(true, $attr, true)) {
            $method = substr($method, 1);
        }

        $command = new Command($method, $attr);

        foreach ($children as $childMethod => $childChildren) {
            if (is_int($childMethod)) {
                list($childMethod, $childChildren) = array($childChildren, array());
            }

            $command->addChild(Command::createFromMap($childMethod, $childChildren));
        }

        return $command;
    }

    /**
     * Constructor
     *
     * Available attribute keys:
     *
     * endChain: will end the chain and return the value of the command.
     * returnToBase: chain of command will return to base after command.
     *
     * @param string $methodName
     * @param array  $attributes
     */
    public function __construct($methodName, array $attributes = array())
    {
        $defaultAttributes = array(
            'endChain' => false,
            'returnToBase' => false
        );

        $this->methodName = $methodName;
        $this->attributes = $attributes + $defaultAttributes;
        $this->children = array();
    }

    /**
     * Returns method name.
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Returns if the command is set to end the chain.
     *
     * @return boolean
     */
    public function willEndChain()
    {
        return $this->attributes['endChain'];
    }

    /**
     * Returns if the command is set to return to base of chain.
     *
     * @return boolean
     */
    public function willReturnToBase()
    {
        return $this->attributes['returnToBase'];
    }

    /**
     * Adds a command as a child.
     *
     * @param  \Chainnn\Command $command
     * @return \Chainnn\Command
     *
     * @throws \Chainnn\Exception\LogicException
     */
    public function addChild(Command $command)
    {
        if ($this->willEndChain()) {
            throw new LogicException('Cannot add child command as parent is set to end the chain.');
        }

        if ($this->willReturnToBase()) {
            throw new LogicException('Cannot add child command as parent is set to return to base.');
        }

        $this->children[$command->getMethodName()] = $command;
        $command->setParent($this);

        return $this;
    }

    /**
     * Returns if a command is a child.
     *
     * @param  \Chainnn\Command $command
     * @return boolean
     */
    public function hasChild(Command $command)
    {
        return array_key_exists($command->getMethodName(), $this->children);
    }

    /**
     * Returns a specific child command.
     *
     * @param  \Chainnn\Command|string $command Method name if string.
     * @return \Chainnn\Command|null
     */
    public function getChild($command)
    {
        if ($command instanceof Command) {
            if ($this->hasChild($command)) {
                return $command;
            } else {
                return null;
            }
        }

        return isset($this->children[$command]) ? $this->children[$command] : null;
    }

    /**
     * Sets the commands parent.
     *
     * @param  \Chainnn\Command $command
     * @return \Chainnn\Command
     */
    public function setParent(Command $command)
    {
        $this->parent = $command;

        return $this;
    }

    /**
     * Gets the parent command.
     *
     * @return \Chainnn\Command
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Checks if a given command is the parent.
     *
     * @param  \Chainnn\Command $command
     * @return boolean
     */
    public function isParent(Command $command)
    {
        return $command === $this->parent;
    }

    /**
     * Returns if the command has a parent.
     *
     * @return boolean
     */
    public function hasParent()
    {
        return null !== $this->parent;
    }
}
