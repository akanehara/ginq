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



class UnfoldIterator implements \Iterator
{
    /**
     * @var mixed
     */
    protected $seed0;

    /**
     * @var mixed
     */
    protected $seed;

    /**
     * @var \Closure
     */
    protected $gen;

    /**
     * @var null|array
     */
    protected $x;

    /**
     * @var int
     */
    protected $i;

    /**
     * @var mixed
     */
    protected $v;

    /**
     * @param $seed
     * @param $generator
     */
    public function __construct($seed, $generator)
    {
        $this->seed0 = $seed;
        $this->gen  = $generator;
    }

    public function current()
    {
        return $this->v;
    }

    public function next()
    {
        $this->i++;
        $this->fetch();
    }

    public function key()
    {
        return $this->i;
    }

    public function valid()
    {
        return !empty($this->x);
    }

    public function rewind()
    {
        $this->i = 0;
        $this->seed = $this->seed0;
        $this->fetch();
    }

    protected function fetch()
    {
        $gen = $this->gen;
        $this->x = $gen($this->seed);
        if (!is_null($this->x) && !is_array($this->x)) {
            $type = gettype($this->x);
            throw new \RuntimeException("unfold: Generator function returned $type. array (2 elements) or null expected.");
        }
        @list($this->v, $this->seed) = $this->x;
    }
}

