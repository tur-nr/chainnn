<?php

/**
 * SQL Builder Example
 *
 * This class demonstrates a more complex chaining hierarchy. The code within
 * the functions is not needed for this example but I'm sure you can use you
 * imagination.
 */
class SQLBuilder implements \Chainnn\Chainable
{
    /**
     * This method is part of the Chainable interface, simply return an array
     * representation or construct a chain of command.
     *
     * @return array|Chainnn\ChainOfCommand
     */
    public function getChainOfCommand()
    {
        return array(
            'select' => array(
                'from',
                '^where'
            ),
            'update' => array(
                'set',
                '^where'
            ),
            'insert' => array(
                'into'
            ),
            'delete' => array(
                'from',
                '^where'
            ),
            '$query'
        );
    }

    /**
     * Helper function to constructor a new chain.
     *
     * @return \Chainnn\Chain
     */
    public function getBuilder()
    {
        return new \Chainnn\Chain($this);
    }

    /**
     * Imagine they are all filled with lovely standards complaint code.
     */
    public function select($fields) { }
    public function update($table) { }
    public function insert(array $fieldValues) { }
    public function delete() { }
    public function from($table) { }
    public function where($clause) { }
    public function set(array $fieldValues) { }
    public function into($table) { }
    public function query() { }
}

// Spl auto loader
require('SplClassLoader.php');
$loader = new SplClassLoader(null, implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src')));
$loader->register();

// SQL builder
$query = new SQLBuilder();

// Run some successful queries
$query->getBuilder()
    ->select('*')
    ->from('foo')
    ->where('bar = 1')
    ->query()
; // SELECT * FROM foo WHERE bar = 1;

$query->getBuilder()
    ->update('foo')
    ->set(array('bar' => 2))
    ->where('bar = 1')
    ->query()
; // UPDATE foo SET bar = 2 WHERE bar = 1;

$query->getBuilder()
    ->insert(array('foo' => 3))
    ->into('foo')
    ->query()
; // INSERT INTO foo (bar) VALUES (3);

$query->getBuilder()
    ->delete()
    ->from('foo')
    ->where('bar = 2')
    ->query()
; // DELETE FROM foo WHERE bar = 2

$query->getBuilder()
    ->delete()
    ->from('foo')
    ->where('bar = 2')
    ->query()
; // DELETE FROM foo WHERE bar = 2

// Try go against the hierarchy
try {
    $query->getBuilder()
        ->select('*')
        ->from('foo')
        ->into('bar')
        ->query()
    ;
} catch (\Chainnn\Exception\RuntimeException $e) {
    echo $e->getMessage(); // Invalid use of chain
}