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

require_once dirname(__DIR__) . "/iterator.php";

/**
 * TakeWhileIterator
 * @package Ginq
 */
class TakeWhileIterator implements \Iterator
{
    private $it;
    private $predicate;

    private $i;
    private $valid;

    public function __construct($xs, $predicate)
    {
        $this->it = \Ginq\Core\iterator($xs);
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
        $p = $this->predicate;
        return $this->it->valid()
            && $p($this->it->current(), $this->it->key());
    }
}
