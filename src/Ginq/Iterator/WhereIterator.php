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

require_once dirname(__DIR__) . "/iter.php";

/**
 * WhereIterator
 * @package Ginq
 */
class WhereIterator implements \Iterator
{
    private $it;
    private $predicate;

    private $i;

    public function __construct($xs, $predicate)
    {
        $this->it = \Ginq\iter($xs);
        $this->predicate = $predicate;
    }

    public function current()
    {
        return $this->it->current();
    }

    public function key() 
    {
        return $this->it->key();
    }
    
    private function nextSatisfied() {
        $p = $this->predicate;
        while ($this->it->valid()) {
            if ($p($this->it->current(), $this->it->key())) {
                break;
            } else {
                $this->it->next();
            }
        }
    }

    public function next()
    {
        $this->it->next();
        $this->i++;
        $this->nextSatisfied();
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
        $this->nextSatisfied();
    }

    public function valid()
    {
        return $this->it->valid();
    }
}
