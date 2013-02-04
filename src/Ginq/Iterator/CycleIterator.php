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
namespace Ginq\Iterator;

/**
 * CycleIterator
 * @package Ginq
 */
class CycleIterator implements \Iterator
{
    private $i;
    private $it;

    public function __construct($xs)
    {
        $this->it = \Ginq\iter($xs);
    }

    public function current()
    {
        return $this->it->current();
    }

    public function key() 
    {
        return $this->i;
    }

    public function next()
    {
        $this->i++;
        $this->it->next();
        $v = $this->it->valid();
        if (!$v) {
            $this->it->rewind();
        }
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
    }

    public function valid()
    {
        return $this->it->valid();
    }
}
