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

/**
 * TakeIterator
 * @package Ginq
 */
class Ginq_Iterator_TakeIterator implements Iterator
{
    private $it;
    private $n;

    private $i;

    public function __construct($xs, $n)
    {
        $this->it = iter($xs);
        $this->n  = $n;
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
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
    }

    public function valid()
    {
        if ($this->i < $this->n) {
            return $this->it->valid();
        } else {
            return false;
        }
    }
}
