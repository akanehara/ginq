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
namespace Ginq\core;

/**
 * IterProviderGenImpl
 * @package Ginq
 */
class IterProviderGenImpl implements IterProvider
{
    public function zero()
    {
        return new RewindableGenerator(self::_zero());
    }

    protected static function _zero()
    {
        while (false) { yield null; }
    }

    public function range($start, $stop, $step)
    {
        return new RewindableGenerator(self::_range($start, $stop, $step));
    }

    protected static function _range($start, $stop, $step)
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
        return new RewindableGenerator(self::_rangeInf($start, $step));
    }

    protected static function _rangeInf($start, $step)
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
        return new RewindableGenerator(self::_repeat($x));
    }

    protected static function _repeat($x)
    {
        while (true) {
            yield $x;
        }
    }

    public function cycle($xs)
    {
        return new RewindableGenerator(self::_cycle($xs));
    }

    protected static function _cycle($xs)
    {
        while (true) {
            foreach ($xs as $k => $v) {
                yield $k => $v;
            }
        }
    }

    public function rehash($xs)
    {
        return new RewindableGenerator(self::_rehash($xs));
    }

    protected static function _rehash($xs)
    {
        foreach ($xs as $k => $v) {
            yield $v;
        }
    }

    public function select($xs, $valueSelector, $keySelector)
    {
        return new RewindableGenerator(self::_select($xs, $valueSelector, $keySelector));
    }

    protected static function _select($xs, $valueSelector, $keySelector)
    {
        foreach ($xs as $k => $v) {
            yield $keySelector($v, $k) => $valueSelector($v, $k);
        }
    }

    public function where($xs, $predicate)
    {
        return new RewindableGenerator(self::_where($xs, $predicate));
    }

    protected static function _where($xs, $predicate)
    {
        foreach ($xs as $k => $v) {
            if ($predicate($v, $k)) {
                yield $k => $v;
            }
        }
    }

    public function reverse($xs)
    {
        return new RewindableGenerator(self::_reverse($xs));
    }

    protected static function _reverse($xs)
    {
        $items = array();
        $len = 0;
        foreach ($xs as $k => $v) {
            array_push($items, array($k, $v));
            $len++;
        }
        for ($i = $len - 1; 0 <= $i; $i--) {
            $x = $items[$i];
            yield $x[0] => $x[1];
        }
    }

    public function take($xs, $n)
    {
        return new RewindableGenerator(self::_take($xs, $n));
    }

    protected static function _take($xs, $n)
    {
        $i = $n;
        foreach ($xs as $k => $v) {
            if ($i <= 0) {
                break;
            } else {
                yield $k => $v;
                $i--;
            }
        }
    }

    public function drop($xs, $n)
    {
        return new RewindableGenerator(self::_drop($xs, $n));
    }

    protected static function _drop($xs, $n)
    {
        $i = $n;
        foreach ($xs as $k => $v) {
            if (0 < $i) {
                $i--;
            } else {
                yield $k => $v;
            }
        }
    }

    public function takeWhile($xs, $predicate)
    {
        return new RewindableGenerator(self::_takeWhile($xs, $predicate));
    }

    protected static function _takeWhile($xs, $predicate)
    {
        foreach ($xs as $k => $v) {
            if ($predicate($v, $k)) {
                yield $k => $v;
            } else {
                break; 
            }
        }
    }

    public function dropWhile($xs, $predicate)
    {
        return new RewindableGenerator(self::_dropWhile($xs, $predicate));
    }

    protected static function _dropWhile($xs, $predicate)
    {
        $xs->rewind();
        while ($xs->valid()) {
            if ($predicate($xs->current(), $xs->key())) {
                $xs->next();
            } else {
                break;
            }
        }
        while ($xs->valid()) {
            yield $xs->key() => $xs->current();
            $xs->next();
        }
    }

    public function concat($xs, $ys)
    {
        return new RewindableGenerator(self::_concat($xs, $ys));
    }

    protected static function _concat($xs, $ys)
    {
        foreach ($xs as $k => $v) {
            yield $k => $v;
        }
        foreach ($ys as $k => $v) {
            yield $k => $v;
        }
    }

    public function selectMany($xs, $manySelector)
    {
        return new RewindableGenerator(self::_selectMany($xs, $manySelector));
    }

    protected static function _selectMany($xs, $manySelector)
    {
        foreach ($xs as $k0 => $v0) {
            foreach ($manySelector($v0, $k0) as $k1 => $v1) {
                yield $k1 => $v1;
            }
        }
    }

    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        return new RewindableGenerator(
            self::_selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
        );
    }

    protected static function _selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        foreach ($xs as $k0 => $v0) {
            foreach ($manySelector($v0, $k0) as $k1 => $v1) {
                $k = $keyJoinSelector($v0, $v1, $k0, $k1);
                $v = $valueJoinSelector($v0, $v1, $k0, $k1);
                yield $k => $v;
            }
        }
    }

    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        return new RewindableGenerator(
            self::_zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
        );
    }

    protected static function _zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        $xs->rewind();
        $ys->rewind();
        while ($xs->valid() && $ys->valid()) {
            $k = $keyJoinSelector(
                $xs->current(), $ys->current(), $xs->key(), $ys->key()
            );
            $v = $valueJoinSelector(
                $xs->current(), $ys->current(), $xs->key(), $ys->key()
            );
            yield $k => $v;
            $xs->next();
            $ys->next();
        }
    }

    public function groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector)
    {
        return new RewindableGenerator(
            self::_groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector)
        );
    }

    protected static function _groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector)
    {
        foreach (Lookup::from($xs, $groupingKeySelector) as $k => $ys) {
            $group = self::_select($ys,
                $elementSelector,
                function($v, $k) { return $k; }
            );
            yield $k => $groupSelector($group, $k);
        }
    }

}


