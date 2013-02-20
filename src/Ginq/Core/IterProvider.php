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

use Ginq\Core\Selector;
use Ginq\Core\JoinSelector;
use Ginq\Core\Predicate;
use Ginq\Core\Comparer;

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
     * @return \Iterator
     */
    public function repeat($x);

    /**
     * @param \Iterator $xs
     * @return \Iterator
     */
    public function cycle($xs);

    /**
     * @param \Iterator $xs
     * @return \Iterator
     */
    public function renum($xs);

    /**
     * @param \Iterator $xs
     * @param \Closure $fn
     * @return \Iterator
     */
    public function each($xs, $fn);

    /**
     * @param \Iterator $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Iterator
     */
    public function select($xs, $valueSelector, $keySelector);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function where($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @return \Iterator
     */
    public function reverse($xs);

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Iterator
     */
    public function take($xs, $n);

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Iterator
     */
    public function drop($xs, $n);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function takeWhile($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function dropWhile($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @return \Iterator
     */
    public function concat($xs, $ys);

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @return \Iterator
     */
    public function selectMany($xs, $manySelector);

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Iterator
     */
    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);

    /**
     * @param \Iterator $outer
     * @param \Iterator $inner
     * @param Selector $outerKeySelector
     * @param Selector $innerKeySelector
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Iterator
     */
    public function join($outer, $inner, $outerkeySelector, $innerKeySelector, $valueJoinSelector, $keyJoinSelector);

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Iterator
     */
    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector);

    /**
     * @param \Iterator $xs
     * @param Selector $groupingKeySelector
     * @param Selector $elementSelector
     * @param Selector $groupSelector
     * @return \Iterator
     */
    public function groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector);

    /**
     * @param \Iterator $xs
     * @param Comparer $comparer
     * @return \Iterator
     */
    public function orderBy($xs, $comparer);

    /**
     * @param \Iterator $xs
     * @return \Iterator
     */
    public function memoize($xs);
}

