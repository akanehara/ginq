<?php
/**
 * Ginq: Generator INtegrated Query
 * Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.5 or later
 *
 * @author     Atsushi Kanehara <akanehara@gmail.com>
 * @copyright  Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package    Ginq
 */

require_once dirname(__FILE__) . "/IterProvider.php";
require_once dirname(__FILE__) . "/Lookup.php";

/**
 * IterProviderGenImpl
 * @package Ginq
 */
class IterProviderGenImpl implements IterProvider
{
    public function zero()
    {
        while (false) { yield null; }
    }

    public function range($start, $stop, $step)
    {
        if (0 <= $step) {
            for($i = $start; $i <= $stop; $i += $step) {
                yield $i;
            }
        } else {
            for($i = $start; $i >= $stop; $i += $step) {
                yield $i;
            }
        }
    }

    public function rangeInf($start, $step)
    {
        if (0 <= $step) {
            for($i = $start; true; $i += $step) {
                yield $i;
            }
        } else {
            for($i = $start; true; $i += $step) {
                yield $i;
            }
        }
    }

    public function repeat($x)
    {
        while (true) {
            yield $x;
        }
    }

    public function cycle($xs)
    {
        while (true) {
            foreach ($xs as $x) {
                yield $x;
            }
        }
    }

    public function select($xs, $selector)
    {
        foreach ($xs as $x) {
            yield $selector($x);
        }
    }

    public function where($xs, $predicate)
    {
        foreach ($xs as $x) {
            if ($predicate($x)) {
                yield $x;
            }
        }
    }

    public function take($xs, $n)
    {
        $i = $n;
        foreach ($xs as $x) {
            if ($i <= 0) {
                break;
            } else {
                yield $x;
                $i--;
            }
        }
    }

    public function drop($xs, $n)
    {
        $i = $n;
        foreach ($xs as $x) {
            if (0 < $i) {
                $i--;
            } else {
                yield $x;
            }
        }
    }

    public function takeWhile($xs, $predicate)
    {
        foreach ($xs as $x) {
            if ($predicate($x)) {
                yield $x;
            } else {
                break; 
            }
        }
    }

    public function dropWhile($xs, $predicate)
    {
        $xs->rewind();
        while ($xs->valid()) {
            if ($predicate($xs->current())) {
                $xs->next();
            } else {
                break;
            }
        }
        while ($xs->valid()) {
            yield $xs->current();
            $xs->next();
        }
    }

    public function concat($xs, $ys)
    {
        foreach ($xs as $x) {
            yield $x;
        }
        foreach ($ys as $y) {
            yield $y;
        }
    }

    public function selectMany($xs, $manySelector)
    {
        foreach ($xs as $x) {
            foreach ($manySelector($x) as $y) {
                yield $y;
            }
        }
    }

    public function selectManyWithJoin(
        $xs, $manySelector, $joinSelector)
    {
        foreach ($xs as $x) {
            foreach ($manySelector($x) as $y) {
                yield $joinSelector($x, $y);
            }
        }
    }

    public function zip($xs, $ys, $joinSelector)
    {
        $xs->rewind();
        $ys->rewind();
        while ($xs->valid() && $ys->valid()) {
            yield $joinSelector($xs->current(), $ys->current());
            $xs->next();
            $ys->next();
        }
    }

    public function groupBy($xs, $keySelector, $elementSelector)
    {
        foreach (Lookup::from($xs, $keySelector) as $xs) {
            yield self::from($xs)->select($elementSelector);
        }
    }

}


