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

namespace Ginq\Iterator;

/**
 * ZeroIterator
 * @package Ginq
 */
class ZeroIterator implements \Iterator
{
    public function current()
    {
        return null;
    }

    public function key() 
    {
        return null;
    }

    public function next()
    {
    }

    public function rewind()
    {
    }

    public function valid()
    {
        return false;
    }
}
