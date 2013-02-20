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

/**
 * RangeIterator
 * @package Ginq
 */
class RangeIterator implements \Iterator
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $stop;

    /**
     * @var int
     */
    private $step;

    /**
     * @var int
     */
    private $i;

    /**
     * @var int
     */
    private $x;

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     */
    public function __construct($start, $stop,  $step)
    {
        $this->start = $start;
        $this->stop  = $stop;
        $this->step  = $step;
    }

    public function current()
    {
        return $this->x;
    }

    public function key() 
    {
        return $this->i;
    }

    public function next()
    {
        $this->i++;
        $this->x += $this->step;
    }

    public function rewind()
    {
        $this->i = 0;
        $this->x = $this->start;
    }

    public function valid()
    {
        if (0 <= $this->step) {
            return $this->x <= $this->stop;
        } else {
            return $this->stop <= $this->x;
        }
    }
}
