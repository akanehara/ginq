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
 * SelectIterator
 * @package Ginq
 */
class SelectIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var Selector
     */
    private $valueSelector;

    /**
     * @var Selector
     */
    private $keySelector;

    /**
     * @var mixed
     */
    protected $v;

    /**
     * @var mixed
     */
    protected $k;

    /**
     * @param array|\Traversable $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     */
    public function __construct($xs, $valueSelector, $keySelector)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->valueSelector = $valueSelector;
        $this->keySelector = $keySelector;
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
        $this->it->next();
        $this->fetch();
    }

    public function rewind()
    {
        $this->it->rewind();
        $this->fetch();
    }

    public function valid()
    {
        return $this->it->valid();
    }

    private function fetch()
    {
        if ($this->it->valid()) {
            $v = $this->it->current();
            $k = $this->it->key();
            $this->v = $this->valueSelector->select($v, $k);
            $this->k = $this->keySelector->select($v, $k);
        }
    }
}
