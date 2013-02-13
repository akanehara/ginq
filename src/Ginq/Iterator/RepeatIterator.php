<?php
/**
 * Ginq: Generator INtegrated Query
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
namespace Ginq\Core\Iterator;

/**
 * RepeatIterator
 * @package Ginq
 */
class RepeatIterator implements \Iterator
{
    private $i;
    private $x;

    public function __construct($x)
    {
        $this->x = $x;
    }

    public function current()
    {
        return $this->x;
    }

    public function key() 
    {
        return $this->i;
    }

    public function next()
    {
        $this->i++;
    }

    public function rewind()
    {
        $this->i = 0;
    }

    public function valid()
    {
        return true;
    }
}
