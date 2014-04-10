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
 * ExceptIterator
 * @package Ginq
 */
class ExceptIterator implements \Iterator
{
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
    private $masked;

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
        $this->it0->next();
        $this->fetch();
    }

    public function rewind()
    {
        $this->masked = new Set($this->eqComparer, $this->it1);
        $this->it0->rewind();
        $this->fetch();
    }

    public function valid()
    {
        return $this->it0->valid();
    }

    protected function fetch()
    {
        while ($this->it0->valid()) {
            $this->v = $this->it0->current();
            $this->k = $this->it0->key();
            if ($this->masked->add($this->v)) {
                return;
            } else {
                $this->it0->next();
            }
        }
    }
}

