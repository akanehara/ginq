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
namespace Ginq\Core;

use Ginq\Iterator\ZeroIterator;
use Ginq\Iterator\RangeIterator;
use Ginq\Iterator\RangeInfIterator;
use Ginq\Iterator\CycleIterator;
use Ginq\Iterator\RepeatIterator;
use Ginq\Iterator\RenumIterator;
use Ginq\Iterator\EachIterator;
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
use Ginq\Iterator\MemoizeIterator;

/**
 * IterProviderIterImpl
 * @package Ginq
 */
class IterProviderIterImpl implements IterProvider
{

    /**
     * @return \Ginq\Iterator\ZeroIterator
     */
    public function zero()
    {
        return new ZeroIterator();
    }

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Ginq\Iterator\RangeIterator
     */
    public function range($start, $stop, $step)
    {
        return new RangeIterator($start, $stop, $step);
    }

    /**
     * @param int $start
     * @param int $step
     * @return \Ginq\Iterator\RangeInfIterator
     */
    public function rangeInf($start, $step)
    {
        return new RangeInfIterator($start, $step);
    }

    /**
     * @param mixed $x
     * @return \Ginq\Iterator\RepeatIterator
     */
    public function repeat($x)
    {
        return new RepeatIterator($x);
    }

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\CycleIterator
     */
    public function cycle($xs)
    {
        return new CycleIterator($xs);
    }

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\RenumIterator
     */
    public function renum($xs)
    {
        return new RenumIterator($xs);
    }


    /**
     * @param \Iterator $xs
     * @param \Closure $fn
     * @return \Ginq\Iterator\EachIterator
     */
    public function each($xs, $fn)
    {
        return new EachIterator($xs, $fn);
    }

    /**
     * @param \Iterator $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Ginq\Iterator\SelectIterator
     */
    public function select($xs, $valueSelector, $keySelector)
    {
        return new SelectIterator($xs, $valueSelector, $keySelector);
    }

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\WhereIterator
     */
    public function where($xs, $predicate)
    {
        return new WhereIterator($xs, $predicate);
    }

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\ReverseIterator
     */
    public function reverse($xs)
    {
        return new ReverseIterator($xs);
    }

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Ginq\Iterator\TakeIterator
     */
    public function take($xs, $n)
    {
        return new TakeIterator($xs, $n);
    }

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Ginq\Iterator\DropIterator
     */
    public function drop($xs, $n)
    {
        return new DropIterator($xs, $n);
    }

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\TakeWhileIterator
     */
    public function takeWhile($xs, $predicate)
    {
        return new TakeWhileIterator($xs, $predicate);
    }

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\DropWhileIterator
     */
    public function dropWhile($xs, $predicate)
    {
        return new DropWhileIterator($xs, $predicate);
    }

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @return \Ginq\Iterator\ConcatIterator
     */
    public function concat($xs, $ys)
    {
        return new ConcatIterator($xs, $ys);
    }

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @return \Ginq\Iterator\SelectManyIterator
     */
    public function selectMany($xs, $manySelector)
    {
        return new SelectManyIterator($xs, $manySelector);
    }

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Ginq\Iterator\SelectManyWithJoinIterator
     */
    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        return new SelectManyWithJoinIterator($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);
    }

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Ginq\Iterator\ZipIterator
     */
    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        return new ZipIterator($xs, $ys, $valueJoinSelector, $keyJoinSelector);
    }

    /**
     * @param \Iterator $xs
     * @param Selector $keySelector
     * @param Selector $elementSelector
     * @param Selector $groupSelector
     * @return \Ginq\Iterator\GroupByIterator
     */
    public function groupBy($xs, $keySelector, $elementSelector, $groupSelector)
    {
        return new GroupByIterator($xs, $keySelector, $elementSelector, $groupSelector);
    }

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\MemoizeIterator
     */
    public function memoize($xs)
    {
        return new MemoizeIterator($xs);
    }
}

