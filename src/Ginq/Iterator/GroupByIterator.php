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
namespace Ginq\Iterator;

use Ginq\Lookup;

require_once dirname(__DIR__) . "/iter.php";

/**
 * GroupByIterator
 * @package Ginq
 */
class GroupByIterator implements \Iterator
{
    private $groupingKeySelector;
    private $elementSelector;
    private $groupSelector;

    private $it;
    private $group;
    private $i;

    public function __construct($xs, $groupingKeySelector, $elementSelector, $groupSelector)
    {
        $this->it = \Ginq\iter($xs);
        $this->groupingKeySelector = $groupingKeySelector;
        $this->elementSelector = $elementSelector;
        $this->groupSelector   = $groupSelector;
    }

    public function current()
    {
        $group = new SelectIterator(
            $this->group->current(),
            $this->elementSelector,
            function($x, $k) { return $k; }
        );
        $f = $this->groupSelector;
        return $f($group, $this->group->key());
    }

    public function key() 
    {
        return $this->group->key();
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
        $f = $this->groupingKeySelector;
        $this->group = Lookup::from($this->it, $f)->getIterator();
    }

    public function valid()
    {
        return $this->group->valid();
    }
}
