<?php

/**
 * Calculator Example
 *
 * This is a simple calculator class to demonstrate a minimal example of how to
 * use Chainnn.
 */
class Calculator
{
    private $answer;

    public function __construct($startingValue = 0)
    {
        $this->answer = (float)$startingValue;
    }

    public function add($value)
    {
        return $this->answer += (float)$value;
    }

    public function sub($value)
    {
        return $this->answer -= (float)$value;
    }

    public function mul($value)
    {
        return $this->answer *= (float)$value;
    }

    public function div($value)
    {
        return $this->answer /= (float)$value;
    }

    public function clear()
    {
        $this->answer = 0;
    }

    public function eq()
    {
        return $this->answer;
    }
}

// Spl auto loader
require('SplClassLoader.php');
$loader = new SplClassLoader(null, implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'src')));
$loader->register();

// Calculator
$calc = new Calculator();

$calc->add(15);   // answer = 15
$calc->mul(3);    // answer = 45
$calc->div(9);    // answer = 5
echo $calc->eq(); // prints 5

echo "\n";

// Implement calculator chain
$chain = new \Chainnn\Chain($calc, array(
    'add', 'sub', 'mul', 'div', '^clear', '$eq'
));

echo $chain->clear() // answer = 0
    ->add(100)       // answer = 100
    ->div(5)         // answer = 20
    ->sub(10)        // answer = 10
    ->mul(3)         // answer = 30
    ->eq()           // prints 30
;