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

use \Ginq\Core\JoinSelector;
use \Ginq\Util\IteratorUtil;

/**
 * ZipIterator
 * @package Ginq
 */
class ZipIterator implements \Iterator
{
    /**
     * @var JoinSelector
     */
    private $valueJoinSelector;

    /**
     * @var JoinSelector
     */
    private $keyJoinSelector;

    /**
     * @var \Iterator
     */
    private $it0;

    /**
     * @var \Iterator
     */
    private $it1;

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
     * @param array|\Traversable $ys
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     */
    public function __construct($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        $this->valueJoinSelector = $valueJoinSelector;
        $this->keyJoinSelector = $keyJoinSelector;
        $this->it0 = IteratorUtil::iterator($xs);
        $this->it1 = IteratorUtil::iterator($ys);
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
        $this->it0->next();
        $this->it1->next();
        $this->fetch();
    }

    public function rewind()
    {
        $this->it0->rewind();
        $this->it1->rewind();
        $this->fetch();
    }

    public function valid()
    {
        return $this->it0->valid() && $this->it1->valid();
    }

    protected function fetch()
    {
        $v0 = $this->it0->current();
        $v1 = $this->it1->current();
        $k0 = $this->it0->key();
        $k1 = $this->it1->key();
        $this->v = $this->valueJoinSelector->select($v0, $v1, $k0, $k1);
        $this->k = $this->keyJoinSelector->select($v0, $v1, $k0, $k1);
    }
}

