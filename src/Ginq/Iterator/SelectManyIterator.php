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

require_once dirname(dirname(__FILE__)) . "/iter.php";

/**
 * SelectManyIterator
 * @package Ginq
 */
class SelectManyIterator implements Iterator
{
    private $manySelector;

    private $outer;
    private $innter;
    private $i;

    public function __construct($xs, $manySelector)
    {
        $this->outer = iter($xs);
        $this->manySelector = $manySelector;
    }

    public function current()
    {
        return $this->inner->current();
    }

    public function key() 
    {
        return $this->i;
    }

    public function next()
    {
        $this->i++;
        $this->inner->next();
        if (!$this->inner->valid()) {
            $this->outer->next();
            $this->nextInner();
        }
    }

    public function rewind()
    {
        $this->i = 0;
        $this->outer->rewind();
        $this->nextInner();
    }

    protected function nextInner()
    {
        if ($this->outer->valid()) {
            $k = $this->manySelector;
            $this->inner = iter(
                $k(
                    $this->outer->current(),
                    $this->outer->key()
                )
            );
        }
    }

    public function valid()
    {
        return $this->inner->valid();
    }
}
