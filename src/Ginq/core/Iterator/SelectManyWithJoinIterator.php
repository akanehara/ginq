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
namespace Ginq\core\iterator;

require_once dirname(__DIR__) . "/iter.php";

/**
 * SelectManyWithJoinIterator
 * @package Ginq
 */
class SelectManyWithJoinIterator implements \Iterator
{
    private $manySelector;
    private $valueJoinSelector;
    private $keyJoinSelector;

    private $outer;
    private $inner;
    private $i;

    public function __construct($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        $this->outer = \Ginq\core\iter($xs);
        $this->manySelector = $manySelector;
        $this->valueJoinSelector = $valueJoinSelector;
        $this->keyJoinSelector = $keyJoinSelector;
    }

    public function current()
    {
        $f = $this->valueJoinSelector;
        return $f(
            $this->outer->current(),
            $this->inner->current(),
            $this->outer->key(),
            $this->inner->key()
        );
    }

    public function key() 
    {
        $f = $this->keyJoinSelector;
        return $f(
            $this->outer->current(),
            $this->inner->current(),
            $this->outer->key(),
            $this->inner->key()
        );
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
            $this->inner = \Ginq\core\iter(
                $k($this->outer->current(), $this->outer->key())
            );
        }
    }

    public function valid()
    {
        return $this->inner->valid();
    }
}
