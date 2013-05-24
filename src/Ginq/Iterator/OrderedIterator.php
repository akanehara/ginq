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

use Ginq\Core\Comparer;
use Ginq\Util\IteratorUtil;

class OrderedIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    protected $src;

    /**
     * @var Comparer
     */
    protected $comparer;

    /**
     * @var array
     */
    protected $sorted;

    /**
     * @var int
     */
    protected $i;

    /**
     * @var int
     */
    protected $len;

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
     * @param Comparer $comparer
     */
    public function __construct($xs, $comparer)
    {
        $this->src = IteratorUtil::iterator($xs);
        $this->comparer = $comparer;
    }

    public function rewind()
    {
        $comparer = $this->comparer;
        $sorted = array();
        foreach ($this->src as $k => $v) {
            array_push($sorted, array($k, $v));
        }
        usort(
            $sorted,
            function($x, $y) use ($comparer) {
                list($k0, $v0) = $x;
                list($k1, $v1) = $y;
                $cmp = $comparer->compare($v0, $v1, $k0, $k1);
                return $cmp;
            }
        );
        $this->sorted = $sorted;
        $this->i = 0;
        $this->len = count($this->sorted);
        $this->fetch();
    }

    public function current()
    {
        return $this->v;
    }

    public function key()
    {
        return $this->k;
    }

    public function valid()
    {
        return $this->i < $this->len;
    }

    public function next()
    {
        $this->i++;
        $this->fetch();
    }

    protected function fetch()
    {
        if ($this->valid()) {
            list($k, $v) = $this->sorted[$this->i];
            $this->v = $v;
            $this->k = $k;
        }
    }
}

