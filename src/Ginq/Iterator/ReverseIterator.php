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
namespace Ginq;

require_once dirname(dirname(__FILE__)) . "/iter.php";

/**
 * ReverseIterator
 * @package Ginq
 */
class ReverseIterator implements \Iterator
{
    private $it;

    private $items;
    private $i;

    public function __construct($xs)
    {
        $this->it = iter($xs);
    }

    public function current()
    {
        return $this->items[$this->i][1];
    }

    public function key() 
    {
        return $this->items[$this->i][0];
    }

    public function next()
    {
        $this->i--;
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
        $this->items = array();
        $len = 0;
        foreach ($this->it as $k => $v) {
            array_push($this->items, array($k, $v));
            $len++;
        }
        $this->i = $len - 1;
    }

    public function valid()
    {
        return 0 <= $this->i;
    }
}

