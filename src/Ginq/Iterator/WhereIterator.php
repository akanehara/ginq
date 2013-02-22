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

use Ginq\Core\Predicate;
use Ginq\Util\IteratorUtil;

/**
 * WhereIterator
 * @package Ginq
 */
class WhereIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var Predicate
     */
    private $predicate;

    /**
     * @var int
     */
    private $i;

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     */
    public function __construct($xs, $predicate)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->predicate = $predicate;
    }

    public function current()
    {
        return $this->it->current();
    }

    public function key() 
    {
        return $this->it->key();
    }
    
    private function nextSatisfied() {
        while ($this->it->valid()) {
            if ($this->predicate->predicate(
                $this->it->current(), $this->it->key()
            )) {
                break;
            } else {
                $this->it->next();
            }
        }
    }

    public function next()
    {
        $this->it->next();
        $this->i++;
        $this->nextSatisfied();
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
        $this->nextSatisfied();
    }

    public function valid()
    {
        return $this->it->valid();
    }
}
