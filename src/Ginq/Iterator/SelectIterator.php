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
 * SelectIterator
 * @package Ginq
 */
class SelectIterator implements \Iterator
{
    private $it;
    private $valueSelector;
    private $keySelector;

    private $i;

    public function __construct($xs, $valueSelector, $keySelector)
    {
        $this->it = \Ginq\Core\iterator($xs);
        $this->valueSelector = $valueSelector;
        $this->keySelector = $keySelector;
    }

    public function current()
    {
        $f = $this->valueSelector;
        return $f($this->it->current(), $this->it->key());
    }

    public function key() 
    {
        $f = $this->keySelector;
        return $f($this->it->current(), $this->it->key());
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
        return $this->it->valid();
    }
}
