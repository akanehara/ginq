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

use Ginq\Core\Lookup;
use Ginq\Core\Selector;
use Ginq\Core\EqualityComparer;
use Ginq\Selector\KeySelector;
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
     * @var Selector
     */
    private $groupSelector;

    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var \Iterator
     */
    private $group;

    /**
     * @var int
     */
    private $i;

    /**
     * @param array|\Traversable $xs
     * @param Selector $groupingKeySelector
     * @param Selector $elementSelector
     * @param Selector $groupSelector
     * @param EqualityComparer $eqComparer
     */
    public function __construct($xs, $groupingKeySelector, $elementSelector, $groupSelector, $eqComparer)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->groupingKeySelector = $groupingKeySelector;
        $this->elementSelector = $elementSelector;
        $this->groupSelector   = $groupSelector;
        $this->eqComparer      = $eqComparer;
    }

    public function current()
    {
        /**
         * @var \Ginq\Core\KeyValuePair $pair
         */
        $pair = $this->group->current();
        $group = new SelectIterator($pair->value,
            $this->elementSelector,
            KeySelector::getInstance()
        );
        return $this->groupSelector->select($group, $pair->key);
    }

    public function key()
    {
        /**
         * @var \Ginq\Core\KeyValuePair $pair
         */
        $pair = $this->group->current();
        return $pair->key;
    }

    public function next()
    {
        $this->i++;
        $this->group->next();
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
        $this->group = Lookup::from(
            $this->it,
            $this->groupingKeySelector,
            $this->eqComparer
        )->getIterator();
    }

    public function valid()
    {
        return $this->group->valid();
    }
}
