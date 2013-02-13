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
 * ConcatIterator
 * @package Ginq
 */
class ConcatIterator implements \Iterator
{
    private $it0;
    private $it1;
    private $it;

    private $i;

    public function __construct($xs, $ys)
    {
        $this->it0 = \Ginq\Core\iterator($xs);
        $this->it1 = \Ginq\Core\iterator($ys);
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
        if ($this->it === $this->it0 && !$this->it->valid()) {
            $this->it = $this->it1;
        }
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it0->rewind();
        $this->it1->rewind();
        if ($this->it0->valid()) {
            $this->it = $this->it0;
        } else {
            $this->it = $this->it1;
        }
    }

    public function valid()
    {
        return $this->it->valid();
    }
}
