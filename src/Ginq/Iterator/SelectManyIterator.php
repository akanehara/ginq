<?php
/**
 * Ginq: `LINQ to Object` inspired DSL for PHP
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

use Ginq\Core\Selector;
use Ginq\Util\IteratorUtil;

/**
 * SelectManyIterator
 * @package Ginq
 */
class SelectManyIterator implements \Iterator
{
    /**
     * @var Selector
     */
    private $manySelector;

    /**
     * @var \Iterator
     */
    private $outer;

    /**
     * @var mixed
     */
    private $outerV;

    /**
     * @var mixed
     */
    private $outerK;

    /**
     * @var \Iterator
     */
    private $inner;

    /**
     * @var mixed
     */
    private $v;

    /**
     * @var mixed
     */
    private $k;

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     */
    public function __construct($xs, $manySelector)
    {
        $this->outer = IteratorUtil::iterator($xs);
        $this->manySelector = $manySelector;
    }

    public function current()
    {
        return $this->v;
    }

    public function key()
    {
        return $this->k;
    }

    public function next()
    {
        $this->inner->next();
        while (!$this->inner->valid()) {
            $this->outer->next();
            $this->fetchOuter();
            if ($this->inner === null) {
                break;
            }
        }
        $this->fetchInner();
    }

    public function rewind()
    {
        $this->outer->rewind();
        $this->fetchOuter();
        $this->fetchInner();
    }

    public function valid()
    {
        return !is_null($this->inner) && $this->inner->valid();
    }

    protected function fetchOuter()
    {
        if ($this->outer->valid()) {
            $this->outerV = $this->outer->current();
            $this->outerK = $this->outer->key();
            $this->inner = IteratorUtil::iterator(
                $this->manySelector->select(
                    $this->outerV,
                    $this->outerK
                )
            );
        } else {
            $this->inner = null;
        }
    }

    protected function fetchInner()
    {
        if ($this->valid()) {
            $this->v = $this->inner->current();
            $this->k = $this->inner->key();
        }
    }
}
