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

use Ginq\Core\Set;
use Ginq\Core\EqualityComparer;

use Ginq\Util\IteratorUtil;

/**
 * DistinctIterator
 * @package Ginq
 */
class DistinctIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var EqualityComparer
     */
    private $eqComparer;

    /**
     * @var \Ginq\Core\Set
     */
    private $seen;

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
     * @param EqualityComparer $eqComparer
     */
    public function __construct($xs, $eqComparer)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->eqComparer = $eqComparer;
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
        while (true) {
            $this->it->next();
            if ($this->it->valid()) {
                if ($this->fetch()) {
                    break;
                }
            } else {
                break;
            }
        }
    }

    public function rewind()
    {
        $this->it->rewind();
        if ($this->it->valid()) {
            $this->seen = new Set($this->eqComparer);
            $this->fetch();
        }
    }

    public function valid()
    {
        return $this->it->valid();
    }

    private function fetch()
    {
        $this->k = $this->it->key();
        $this->v = $this->it->current();
        return $this->seen->add($this->v);
    }
}

