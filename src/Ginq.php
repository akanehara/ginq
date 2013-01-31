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

require_once("Ginq/Lookup.php");

/**
 * Ginq
 * @package Ginq
 */
class Ginq implements IteratorAggregate
{
    protected $iter = null;

    protected function __construct($iter)
    {
        $this->iter = $iter;
    }

    public function getIterator()
    {
        return $this->iter;
    }

    public function toArray()
    {
        $arr = [];
        foreach ($this->iter as $x) {
            array_push($arr, $x);
        }
        return $arr;
    }

    public static function zero()
    {
        return self::from(self::_gen_repeat_n(null, 0));
    }

    public static function from($xs)
    {
        if ($xs instanceof Iterator) {
            return new Ginq($xs);
        } else if ($xs instanceof IteratorAggregate) {
            return new Ginq($xs->getIterator());
        } else if (is_array($xs)) {
            return new Ginq(new ArrayIterator($xs));
        } else {
            throw new InvalidArgumentException(
                'require Iterator or IteratorAggregate or array.');
        }
    }

    public function select($selector)
    {
        return self::from(self::_gen_select(
            $this->iter,
            self::_parse_selector($selector)
        ));
    }

    protected static function _gen_select($xs, $selector)
    {
        foreach ($xs as $x) {
            yield $selector($x);
        }
    }

    public function selectMany($manySelector, $joinSelector = null)
    {
        if (is_null($joinSelector)) {
            return self::from(self::_gen_selectMany(
                $this->iter,
                self::_parse_selector($manySelector)));
        } else {
            return self::from(self::_gen_selectMany_with_select(
                $this->iter,
                self::_parse_selector($manySelector),
                self::_parse_selector($joinSelector)));
        }
    }

    protected static function _gen_selectMany($xs, $manySelector)
    {
        foreach ($xs as $x) {
            foreach ($manySelector($x) as $y) {
                yield $y;
            }
        }
    }

    protected static function _gen_selectMany_with_select(
        $xs, $manySelector, $joinSelector)
    {
        foreach ($xs as $x) {
            foreach ($manySelector($x) as $y) {
                yield $joinSelector($x, $y);
            }
        }
    }

    public function where($predicate)
    {
        return self::from(self::_gen_where($this->iter, self::_parse_predicate($predicate)));
    }

    protected static function _gen_where($xs, $predicate)
    {
        foreach ($xs as $x) {
            if ($predicate($x)) {
                yield $x;
            }
        }
    }

    public static function range($start, $stop = null, $step = 1)
    {
        if (!is_numeric($start)) {
            throw new InvalidArgumentException(
                "self::range() numeric start arguments expected.");
        }
        if (is_null($stop)) {
            return self::from(self::_gen_range_inf($start, $step));
        } else {
            return self::from(self::_gen_range($start, $stop, $step));
        }
    }

    protected static function _gen_range($start, $stop, $step)
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

    protected static function _gen_range_inf($start, $step)
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

    public static function repeat($x, $n = null)
    {
        if (is_null($n)) {
            return self::from(self::_gen_repeat($x));
        } else {
            return self::from(self::_gen_repeat_n($x, $n));
        }
    }

    protected static function _gen_repeat($x)
    {
        while (true) {
            yield $x;
        }
    }

    protected static function _gen_repeat_n($x, $n)
    {
        $i = $n;
        while (0 < $i--) {
            yield $x;
        }
    }

    public static function cycle($xs, $n = null)
    {
        if (is_null($n)) {
            return self::from(self::_gen_cycle($x));
        } else {
            return self::from(self::_gen_cycle_n($x, $n));
        }
    }

    protected static function _gen_cycle($xs)
    {
        while (true) {
            foreach ($xs as $x) {
                yield $x;
            }
        }
    }

    protected static function _gen_cycle_n($xs, $n)
    {
        $i = $n;
        while (0 < $i--) {
            foreach ($xs as $x) {
                yield $x;
            }
        }
    }

    public function zip($rhs, $selector)
    {
        return self::from(self::_gen_zip(
            $this->iter,
            self::from($rhs)->getIterator(),
            self::_parse_selector($selector)));
    }

    protected static function _gen_zip($xs, $ys, $selector)
    {
        $xs->rewind();
        $ys->rewind();
        while ($xs->valid() && $ys->valid()) {
            yield $selector($xs->current(), $ys->current());
            $xs->next();
            $ys->next();
        }
        //$xs->close();
        //$ys->close();
    }

    public function join(
        $inner, $outerKeySelector, $innerKeySelector, $joinSelector)
    {
        $outerKeySelector = self::_parse_selector($outerKeySelector);
        $innerKeySelector = self::_parse_selector($innerKeySelector);
        $innerLookup = Lookup::from($inner, $innerKeySelector);
        return $this->selectMany(
            function($outer) use ($innerLookup, $outerKeySelector) {
                return $innerLookup->get($outerKeySelector($outer));
            },
            $joinSelector
        );
    }
    
    public function any($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->iter as $x) {
            if ($p($x) == true) {
                return true;
            }
        }
        return false;
    }

    public function all($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->iter as $x) {
            if ($p($x) == false) {
                return false;
            }
        }
        return true;
    }

    public function take($n)
    {
        return self::from(self::_gen_take($this->iter, $n));
    }

    protected static function _gen_take($xs, $n)
    {
        $i = $n;
        foreach ($xs as $x) {
            if ($i <= 0) {
                break;
            } else {
                yield $x;
            }
            $i--;
        }
    }

    public function takeWhile($predicate)
    {
        return self::from(self::_gen_takeWhile($this->iter, self::_parse_predicate($predicate)));
    }

    protected static function _gen_takeWhile($xs, $predicate)
    {
        foreach ($xs as $x) {
            if ($predicate($x)) {
                yield $x;
            } else {
                break; 
            }
        }
    }

    public function dropWhile($predicate)
    {
        return self::from(self::_gen_dropWhile($this->iter, self::_parse_predicate($predicate)));
    }

    protected static function _gen_dropWhile($xs, $predicate)
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

    public function groupBy($keySelector, $elementSelector = null) {
        if (is_null($elementSelector)) {
            $elementSelector = function($x) { return $x; };
        }
        return self::from(self::_gen_groupBy(
            $this->iter, $keySelector, $elementSelector
        ));
    }

    protected static function _gen_groupBy($xs, $keySelector, $elementSelector) {
        foreach (Lookup::from($xs, $keySelector) as $xs) {
            yield self::from($xs)->select($elementSelector);
        }
    }

    public static function concat($lhs, $rhs) {
    }

    protected function toLookup($keySelector) {
    }

    private static function _parse_selector($selector)
    {
        if (is_string($selector)) {
            return function($x) use ($selector) {
                if (is_array($x)) {
                    return @$x[$selector];
                } else if (is_object($x)) {
                    return @$x->{$selector};
                } else {
                    $type = gettype($x);
                    throw new DomainException("'$type' object has no key or field"); 
                }
            };
        } else if (is_callable($selector)) {
            return $selector;
        } else {
            $type = gettype($selector);
            throw new InvalidArgumentException(
                "'selector' string or callable expected, got $type");
        }
    }

    private static function _parse_predicate($predicate)
    {
        if (is_callable($predicate)) {
            return $predicate;
        } else {
            $type = gettype($predicate);
            throw new InvalidArgumentException(
                "'predicate' callable expected, got $type");
        }
    }

}

