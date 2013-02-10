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

use Chainnn\Exception\RuntimeException;

/**
 * Chain
 *
 * The Chain class creates chaining mechanism for a object. It either takes an
 * instance of Chainable or an object of any class along with a chain of
 * command.
 *
 * @see \Chainnn\ChainOfCommand
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
class Chain
{
    /**
     * @var \Chainnn\Chainable|object $object
     */
    public $object;

    /**
     * @var \Chainnn\ChainOfCommand $chainOfCommand
     */
    public $chainOfCommand;

    /**
     * @var \ReflectionMethod[]
     */
    protected $methodList;

    /**
     * Constructor
     *
     * @param \Chainnn\Chainable|object     $object
     * @param \Chainnn\ChainOfCommand|array $chainOfCommand
     *
     * @throws \Chainnn\Exception\RuntimeException
     */
    public function __construct($object, $chainOfCommand = null)
    {
        if (! is_object($object)) {
            throw new RuntimeException('Requires type object, got' . gettype($object));
        }

        $this->object     = $object;
        $this->methodList = array();

        $reflection = new \ReflectionObject($object);
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $isMagic = preg_match('/^_{2}/', $method->getName());
            $chainableMethod = $method->getName() == 'getChainOfCommand';

            if (!$isMagic && !$chainableMethod) {
                $this->methodList[$method->getName()] = $method;
            }
        }

        if (null === $chainOfCommand) {
            if ($this->object instanceof Chainable) {
                $chainOfCommand = $this->object->getChainOfCommand();
            }
        }

        if (is_array($chainOfCommand)) {
            $chainOfCommand = ChainOfCommand::createFromArrayMap($chainOfCommand);
        }

        if (! $chainOfCommand instanceof ChainOfCommand) {
            throw new RuntimeException('Could not set a chain of command.');
        }

        $this->chainOfCommand = $chainOfCommand;
    }

    /**
     * This magic method will catch any commands on the chain of command and
     * invoke them, creating the chaining mechanism.
     *
     * @param  string               $method
     * @param  array                $params
     * @return \Chainnn\Chain|mixed
     *
     * @throws \Chainnn\Exception\RuntimeException
     */
    public function __call($method, array $params)
    {
        if (! $this->object) {
            throw new RuntimeException('No object set on chain.');
        }

        if (! $this->chainOfCommand) {
            throw new RuntimeException('No chain of command set.');
        }

        $command = $this->chainOfCommand->findNextAvailableCommand($method);

        if (! $command) {
            throw new RuntimeException("{$method}() is not available on the chain.");
        }

        if ($command->willReturnToBase() || $command->willEndChain()) {
            $this->chainOfCommand->setCurrentCommand();
        } else {
            $this->chainOfCommand->setCurrentCommand($command);
        }

        if (! array_key_exists($method, $this->methodList)) {
            throw new RuntimeException("No method {$method}() exists on the object.");
        }

        $methodReflection = $this->methodList[$method];
        $result = $methodReflection->invokeArgs($this->object, $params);

        return ($command->willEndChain()) ? $result : $this;
    }
}
