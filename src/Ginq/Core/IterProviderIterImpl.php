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

namespace Ginq\Core;

use Ginq\Iterator\BufferIterator;
use Ginq\Iterator\BufferWithPaddingIterator;
use Ginq\Iterator\DistinctIterator;
use Ginq\Iterator\ExceptIterator;
use Ginq\Iterator\GroupJoinIterator;
use Ginq\Iterator\IntersectIterator;
use Ginq\Iterator\LazyRepeatIterator;
use Ginq\Iterator\LazySourceIterator;
use Ginq\Iterator\UnfoldIterator;
use Ginq\Iterator\UnionIterator;
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
use Ginq\Iterator\JoinIterator;
use Ginq\Iterator\ZipIterator;
use Ginq\Iterator\GroupByIterator;
use Ginq\Iterator\OrderedIterator;
use Ginq\Iterator\MemoizeIterator;

/**
 * IterProviderIterImpl
 * @package Ginq
 */
class IterProviderIterImpl implements IterProvider
{

    /**
     * @var IterProviderIterImpl
     */
    static private $inst;

    /**
     * @return IterProviderIterImpl
     */
    static final public function getInstance() {
        if (is_null(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * @return \Ginq\Iterator\ZeroIterator
     */
    public function zero()
    {
        return new ZeroIterator();
    }

    /**
     * @param mixed|\Closure $seed
     * @param \Closure $generator seed -> ([v, seed] | null)
     * @return \Ginq\Iterator\UnfoldIterator
     */
    public function unfold($seed, $generator)
    {
        return new UnfoldIterator($seed, $generator);
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
     * @param int|null $count
     * @return \Ginq\Iterator\RepeatIterator
     */
    public function repeat($x, $count)
    {
        return new RepeatIterator($x, $count);
    }

    /**
     * @param /Closure $valueFactory
     * @param int|null $count
     * @return \Ginq\Iterator\RepeatIterator
     */
    public function lazyRepeat($valueFactory, $count)
    {
        return new LazyRepeatIterator($valueFactory, $count);
    }

    /**
     * @param array|\Traversable $xs
     * @return \Ginq\Iterator\CycleIterator
     */
    public function cycle($xs)
    {
        return new CycleIterator($xs);
    }

    /**
     * @param array|\Traversable $xs
     * @return \Ginq\Iterator\RenumIterator
     */
    public function renum($xs)
    {
        return new RenumIterator($xs);
    }


    /**
     * @param array|\Traversable $xs
     * @param \Closure $fn
     * @return \Ginq\Iterator\EachIterator
     */
    public function each($xs, $fn)
    {
        return new EachIterator($xs, $fn);
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Ginq\Iterator\SelectIterator
     */
    public function select($xs, $valueSelector, $keySelector)
    {
        return new SelectIterator($xs, $valueSelector, $keySelector);
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\WhereIterator
     */
    public function where($xs, $predicate)
    {
        return new WhereIterator($xs, $predicate);
    }

    /**
     * @param array|\Traversable $xs
     * @return \Ginq\Iterator\ReverseIterator
     */
    public function reverse($xs)
    {
        return new ReverseIterator($xs);
    }

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Ginq\Iterator\TakeIterator
     */
    public function take($xs, $n)
    {
        return new TakeIterator($xs, $n);
    }

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Ginq\Iterator\DropIterator
     */
    public function drop($xs, $n)
    {
        return new DropIterator($xs, $n);
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\TakeWhileIterator
     */
    public function takeWhile($xs, $predicate)
    {
        return new TakeWhileIterator($xs, $predicate);
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\DropWhileIterator
     */
    public function dropWhile($xs, $predicate)
    {
        return new DropWhileIterator($xs, $predicate);
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @return \Ginq\Iterator\ConcatIterator
     */
    public function concat($xs, $ys)
    {
        return new ConcatIterator($xs, $ys);
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @return \Ginq\Iterator\SelectManyIterator
     */
    public function selectMany($xs, $manySelector)
    {
        return new SelectManyIterator($xs, $manySelector);
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Ginq\Iterator\SelectManyWithJoinIterator
     */
    public function selectManyWithJoin($xs, $manySelector, $resultValueSelector, $resultKeySelector)
    {
        return new SelectManyWithJoinIterator($xs, $manySelector, $resultValueSelector, $resultKeySelector);
    }

    /**
     * @param array|\Traversable $outer
     * @param array|\Traversable $inner
     * @param Selector $outerCompareKeySelector
     * @param Selector $innerCompareKeySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @param EqualityComparer $eqComparer
     * @return \Ginq\Iterator\JoinIterator
     */
    public function join(
            $outer, $inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector,
            $eqComparer)
    {
        return new JoinIterator(
            $outer, $inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector,
            $eqComparer);
    }

    /**
     * @param array|\Traversable $outer
     * @param array|\Traversable $inner
     * @param Selector $outerCompareKeySelector
     * @param Selector $innerCompareKeySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function groupJoin(
        $outer, $inner,
        $outerCompareKeySelector, $innerCompareKeySelector,
        $resultValueSelector, $resultKeySelector,
        $eqComparer)
    {
        return new GroupJoinIterator(
            $outer, $inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector,
            $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Ginq\Iterator\ZipIterator
     */
    public function zip($xs, $ys, $resultValueSelector, $resultKeySelector)
    {
        return new ZipIterator($xs, $ys, $resultValueSelector, $resultKeySelector);
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $keySelector
     * @param Selector $elementSelector
     * @param EqualityComparer $eqComparer
     * @return \Ginq\Iterator\GroupByIterator
     */
    public function groupBy($xs, $keySelector, $elementSelector, $eqComparer)
    {
        return new GroupByIterator($xs, $keySelector, $elementSelector, $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param Comparer $comparer
     * @return \Iterator
     */
    public function orderBy($xs, $comparer)
    {
        return new OrderedIterator($xs, $comparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function distinct($xs, $eqComparer)
    {
       return new DistinctIterator($xs, $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function union($xs, $ys, $eqComparer)
    {
        return new UnionIterator($xs, $ys, $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function intersect($xs, $ys, $eqComparer)
    {
        return new IntersectIterator($xs, $ys, $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function except($xs, $ys, $eqComparer)
    {
        return new ExceptIterator($xs, $ys, $eqComparer);
    }

    /**
     * @param array|\Traversable $xs
     * @return \Ginq\Iterator\MemoizeIterator
     */
    public function memoize($xs)
    {
        return new MemoizeIterator($xs);
    }

    /**
     * @param callable $sourceFactory
     * @return \Iterator
     */
    public function lazySource($sourceFactory)
    {
        return new LazySourceIterator($sourceFactory);
    }

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @return \Iterator
     */
    public function buffer($xs, $chunkSize)
    {
        return new BufferIterator($xs, $chunkSize);
    }

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @param mixed              $padding
     * @return \Iterator
     */
    public function bufferWithPadding($xs, $chunkSize, $padding)
    {
        return new BufferWithPaddingIterator($xs, $chunkSize, $padding);
    }
}

