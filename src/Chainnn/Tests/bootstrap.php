<?php

$required = array(
    array(__DIR__, '..', 'Exception', 'LogicException.php'),
    array(__DIR__, '..', 'Exception', 'RuntimeException.php'),
    array(__DIR__, '..', 'Chain.php'),
    array(__DIR__, '..', 'Chainable.php'),
    array(__DIR__, '..', 'ChainOfCommand.php'),
    array(__DIR__, '..', 'Command.php')
);

foreach ($required as $require) {
    require_once(implode(DIRECTORY_SEPARATOR, $require));
}


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

class ChainableCalculator extends Calculator implements \Chainnn\Chainable
{
    public function getChainOfCommand()
    {
        return array(
            'add',
            'sub',
            'mul',
            'div',
            '^clear',
            '$eq'
        );
    }
}