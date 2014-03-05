<?php
/**
 * Ginq: `LINQ to Object` inspired DSL for PHP
 * Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.3 or later
 *
 * @author     Atsushi Kanehara <akanehara@gmail.com>
 * @copyright  Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package    Ginq
 */

namespace Ginq\Lambda;

/**
 * Class SyntaxError
 * @package Ginq\Lambda
 */
class SyntaxError extends \LogicException
{
    /**
     * @param string $message
     * @param \Exception $prev
     */
    public function __construct($message, $prev = null)
    {
        parent::__construct($message, 0, $prev);
    }
}

