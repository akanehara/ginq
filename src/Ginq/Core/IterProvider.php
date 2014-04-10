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


/**
 * IterProvider
 * @package Ginq
 */
interface IterProvider
{
    /**
     * @return \Iterator
     */
    public function zero();

    /**
     * @param mixed|\Closure $seed
     * @param \Closure $generator seed -> ([v, seed] | null)
     * @return \Iterator
     */
    public function unfold($seed, $generator);

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Iterator
     */
    public function range($start, $stop, $step);

    /**
     * @param int $start
     * @param int $step
     * @return \Iterator
     */
    public function rangeInf($start, $step);

    /**
     * @param mixed $x
     * @param int|null $count
     * @return \Iterator
     */
    public function repeat($x, $count);

    /**
     * @param /Closure $valueFactory
     * @param int|null $count
     * @return \Ginq\Iterator\RepeatIterator
     */
    public function lazyRepeat($valueFactory, $count);

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function cycle($xs);

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function renum($xs);

    /**
     * @param array|\Traversable $xs
     * @param \Closure $fn
     * @return \Iterator
     */
    public function each($xs, $fn);

    /**
     * @param array|\Traversable $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Iterator
     */
    public function select($xs, $valueSelector, $keySelector);

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function where($xs, $predicate);

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function reverse($xs);

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Iterator
     */
    public function take($xs, $n);

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Iterator
     */
    public function drop($xs, $n);

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function takeWhile($xs, $predicate);

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function dropWhile($xs, $predicate);

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @return \Iterator
     */
    public function concat($xs, $ys);

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @return \Iterator
     */
    public function selectMany($xs, $manySelector);

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Iterator
     */
    public function selectManyWithJoin($xs, $manySelector, $resultValueSelector, $resultKeySelector);

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
    public function join(
            $outer, $inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector,
            $eqComparer);

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
        $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Iterator
     */
    public function zip($xs, $ys, $resultValueSelector, $resultKeySelector);

    /**
     * @param array|\Traversable $xs
     * @param Selector $compareKeySelector
     * @param Selector $elementSelector
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function groupBy($xs, $compareKeySelector, $elementSelector, $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @param Comparer $comparer
     * @return \Iterator
     */
    public function orderBy($xs, $comparer);

    /**
     * @param array|\Traversable $xs
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function distinct($xs, $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function union($xs, $ys, $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function intersect($xs, $ys, $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function except($xs, $ys, $eqComparer);

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function memoize($xs);

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @return \Iterator
     */
    public function buffer($xs, $chunkSize);

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @param mixed              $padding
     * @return \Iterator
     */
    public function bufferWithPadding($xs, $chunkSize, $padding);

    /**
     * @param callable $sourceFactory
     * @return \Iterator
     */
    public function lazySource($sourceFactory);
}

