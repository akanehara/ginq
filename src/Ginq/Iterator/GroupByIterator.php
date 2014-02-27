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

use Ginq\Ginq;
use Ginq\Core\Selector;
use Ginq\Core\EqualityComparer;
use Ginq\GroupingGinq;


use Ginq\Util\IteratorUtil;

/**
 * GroupByIterator
 * @package Ginq
 */
class GroupByIterator implements \Iterator
{
    /**
     * @var Selector
     */
    private $groupingKeySelector;

    /**
     * @var Selector
     */
    private $elementSelector;

    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var \Iterator
     */
    private $groups;

    /** @var mixed */
    private $k;

    /** @var mixed */
    private $v;

    /**
     * @param array|\Traversable $xs
     * @param Selector $groupingKeySelector
     * @param Selector $elementSelector
     * @param EqualityComparer $eqComparer
     */
    public function __construct($xs, $groupingKeySelector, $elementSelector, $eqComparer)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->groupingKeySelector = $groupingKeySelector;
        $this->elementSelector     = $elementSelector;
        $this->eqComparer          = $eqComparer;
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
        $this->groups->next();
        $this->fetch();
    }

    public function rewind()
    {
        $this->groups = Ginq::from($this->it)
            ->toLookup(
                $this->groupingKeySelector,
                $this->elementSelector,
                $this->eqComparer
            )
            ->getIterator();
        $this->groups->rewind();
        if ($this->valid()) {
            $this->fetch();
        }
    }

    public function valid()
    {
        return $this->groups->valid();
    }

    protected function fetch()
    {
        /** @var GroupingGinq $gr */
        $gr = $this->groups->current();
        $this->k = $gr->key();
        $this->v = $gr;
    }
}

