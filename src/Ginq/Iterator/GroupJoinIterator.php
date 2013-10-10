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

use Ginq\Core\EqualityComparer;
use Ginq\Ginq;
use Ginq\LookupGinq;
use Ginq\Core\Selector;
use Ginq\GroupingGinq;
use Ginq\Core\JoinSelector;
use Ginq\Selector\ValueSelector;
use Ginq\Util\IteratorUtil;

class GroupJoinIterator implements \Iterator
{
    /**
     * @var LookupGinq
     */
    protected $lookup;

    /**
     * @var \Iterator
     */
    protected $outer;

    /**
     * @var \Iterator
     */
    protected $inner;

    /**
     * @var Selector
     */
    protected $outerCompareKeySelector;

    /**
     * @var Selector
     */
    protected $innerCompareKeySelector;

    /**
     * @var JoinSelector
     */
    protected $resultValueSelector;

    /**
     * @var JoinSelector
     */
    protected $resultKeySelector;

    /**
     * @var EqualityComparer
     */
    protected $eqComparer;

    /**
     * @var mixed
     */
    protected $v;

    /**
     * @var mixed
     */
    protected $k;

    /**
     * @param array|\Traversable $outer
     * @param array|\Traversable $inner
     * @param Selector $outerCompareKeySelector
     * @param Selector $innerCompareKeySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @param EqualityComparer $eqComparer
     */
    public function __construct(
        $outer, $inner,
        $outerCompareKeySelector, $innerCompareKeySelector,
        $resultValueSelector, $resultKeySelector,
        $eqComparer)
    {
        $this->outer = IteratorUtil::iterator($outer);
        $this->inner = IteratorUtil::iterator($inner);
        $this->outerCompareKeySelector = $outerCompareKeySelector;
        $this->innerCompareKeySelector = $innerCompareKeySelector;
        $this->resultValueSelector = $resultValueSelector;
        $this->resultKeySelector = $resultKeySelector;
        $this->eqComparer = $eqComparer;
    }

    public function current()
    {
        return $this->v;
    }

    public function next()
    {
        $this->outer->next();
        $this->fetchIfValid();
    }

    public function key()
    {
        return $this->k;
    }

    public function valid()
    {
        return $this->outer->valid();
    }

    public function rewind()
    {
        $this->outer->rewind();
        $this->lookup = Ginq::from($this->inner)
            ->toLookup(
                $this->innerCompareKeySelector,
                ValueSelector::getInstance()
            );
        $this->fetchIfValid();
    }

    protected function fetchIfValid()
    {
        if ($this->outer->valid()) {
            $outerV  = $this->outer->current();
            $outerK  = $this->outer->key();
            $compareKey = $this->outerCompareKeySelector->select($outerV, $outerK);
            $inners = $this->lookup->get($compareKey);
            $inners = new GroupingGinq(IteratorUtil::iterator($inners), $compareKey);
            $this->v = $this->resultValueSelector->select($outerV, $inners, $outerK, $compareKey);
            $this->k = $this->outer->key();
        }
    }
}

