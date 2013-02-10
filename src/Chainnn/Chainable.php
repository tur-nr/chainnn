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
 * Chainable
 *
 * @author Christopher Turner <turner296@gmail.com>
 */
interface Chainable
{
    /**
     * Returns the chain of command for the chainable object.
     *
     * @return \Chainnn\ChainOfCommand|array
     */
    public function getChainOfCommand();
}
