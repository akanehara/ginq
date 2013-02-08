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
namespace Ginq;

use Ginq\Iterator\ZeroIterator;
use Ginq\Iterator\RangeIterator;
use Ginq\Iterator\RangeInfIterator;
use Ginq\Iterator\CycleIterator;
use Ginq\Iterator\RepeatIterator;
use Ginq\Iterator\RehashIterator;
use Ginq\Iterator\SelectIterator;
use Ginq\Iterator\WhereIterator;
use Ginq\Iterator\ReverseIterator;
use Ginq\Iterator\TakeIterator;
use Ginq\Iterator\DropIterator;
use Ginq\Iterator\TakeWhileIterator;
use Ginq\Iterator\DropWhileIterator;
use Ginq\Iterator\ConcatIterator;
use Ginq\Iterator\SelectManyIterator;
use Ginq\Iterator\SelectManyWithJoinIterator;
use Ginq\Iterator\ZipIterator;
use Ginq\Iterator\GroupByIterator;

/**
 * IterProviderIterImpl
 * @package Ginq
 */
class IterProviderIterImpl implements IterProvider
{

    public function zero()
    {
        return new ZeroIterator();
    }

    public function range($start, $stop, $step)
    {
        return new RangeIterator($start, $stop, $step);
    }

    public function rangeInf($start, $step)
    {
        return new RangeInfIterator($start, $step);
    }

    public function repeat($x)
    {
        return new RepeatIterator($x);
    }

    public function cycle($xs)
    {
        return new CycleIterator($xs);
    }

    public function rehash($xs)
    {
        return new RehashIterator($xs);
    }

    public function select($xs, $selector, $keySelector)
    {
        return new SelectIterator($xs, $selector, $keySelector);
    }

    public function where($xs, $predicate)
    {
        return new WhereIterator($xs, $predicate);
    }

    public function reverse($xs)
    {
        return new ReverseIterator($xs);
    }

    public function take($xs, $n)
    {
        return new TakeIterator($xs, $n);
    }

    public function drop($xs, $n)
    {
        return new DropIterator($xs, $n);
    }

    public function takeWhile($xs, $predicate)
    {
        return new TakeWhileIterator($xs, $predicate);
    }

    public function dropWhile($xs, $predicate)
    {
        return new DropWhileIterator($xs, $predicate);
    }

    public function concat($xs, $ys)
    {
        return new ConcatIterator($xs, $ys);
    }

    public function selectMany($xs, $manySelector)
    {
        return new SelectManyIterator($xs, $manySelector);
    }

    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        return new SelectManyWithJoinIterator($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);
    }

    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        return new ZipIterator($xs, $ys, $valueJoinSelector, $keyJoinSelector);
    }

    public function groupBy($xs, $keySelector, $elementSelector, $groupSelector)
    {
        return new GroupByIterator($xs, $keySelector, $elementSelector, $groupSelector);
    }

}

