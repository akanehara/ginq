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
namespace Ginq;

require_once dirname(dirname(__FILE__)) . "/iter.php";
require_once dirname(dirname(__FILE__)) . "/Lookup.php";

/**
 * GroupByIterator
 * @package Ginq
 */
class GroupByIterator implements \Iterator
{
    private $keySelector;
    private $elementSelector;
    private $groupSelector;

    private $it;
    private $group;
    private $i;

    public function __construct($xs, $keySelector, $elementSelector, $groupSelector)
    {
        $this->it = iter($xs);
        $this->keySelector     = $keySelector;
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
        $f = $this->keySelector;
        $this->group = Lookup::from($this->it, $f)->getIterator();
    }

    public function valid()
    {
        return $this->group->valid();
    }
}
