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

/**
 * Chain of Command
 *
 * The chain of command is used to monitor the chain's current position in
 * the hierarchy, and handles finding the next available command within it.
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class ChainOfCommand
{
    /**
     * @var \Chainnn\Command[] $commandList
     */
    protected $commandList;

    /**
     * @var \Chainnn\Command $current
     */
    protected $current;

    /**
     * Creates a chain of command instance from an array type representation.
     *
     * @param  array                   $map
     * @return \Chainnn\ChainOfCommand
     */
    public static function createFromArrayMap(array $map)
    {
        $chainOfCommand = new ChainOfCommand();

        foreach ($map as $method => $children) {
            if (is_int($method)) {
                list($method, $children) = array($children, array());
            }

            $chainOfCommand->addCommand(Command::createFromMap($method, $children));
        }

        return $chainOfCommand;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commandList = array();
    }

    /**
     * Adds a command to the chain of commands base.
     *
     * @param \Chainnn\Command $command
     */
    public function addCommand(Command $command)
    {
        $this->commandList[$command->getMethodName()] = $command;
    }

    /**
     * Finds the next available command in the chain. It will attempt to
     * look in the children of the current command, then bubble up the
     * hierarchy until a command is found.
     *
     * @param  string                $methodName
     * @return \Chainnn\Command|null
     */
    public function findNextAvailableCommand($methodName)
    {
        if (null !== $this->current) {
            $currentCommand = $this->current;
            do {
                if ($nextCommand = $currentCommand->getChild($methodName)) {
                    return $nextCommand;
                }
            } while ($currentCommand = $currentCommand->getParent());
        }

        return (array_key_exists($methodName, $this->commandList)) ? $this->commandList[$methodName] : null;
    }

    /**
     * Updates the current command.
     *
     * @param \Chainnn\Command $command
     */
    public function setCurrentCommand(Command $command = null)
    {
        $this->current = $command;
    }
}
