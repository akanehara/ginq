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
 * UnionIterator
 * @package Ginq
 */
class UnionIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var \Iterator
     */
    private $it0;

    /**
     * @var \Iterator
     */
    private $it1;

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
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     */
    public function __construct($xs, $ys, $eqComparer)
    {
        $this->it0 = IteratorUtil::iterator($xs);
        $this->it1 = IteratorUtil::iterator($ys);
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
            } else if ($this->it === $this->it0) {
                $this->it = $this->it1;
                $this->it->rewind();
                if ($this->it->valid()) {
                    if ($this->fetch()) {
                        break;
                    }
                } else {
                    break;
                }
            } else {
                break;
            }
        }
    }

    public function rewind()
    {
        $this->it = $this->it0;
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

    protected function fetch()
    {
        $this->k = $this->it->key();
        $this->v = $this->it->current();
        return $this->seen->add($this->v);
    }
}

