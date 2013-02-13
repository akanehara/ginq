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

use Ginq\Core\Selector;
use Ginq\Core\JoinSelector;
use Ginq\Core\Predicate;

/**
 * IterProvider
 * @package Ginq
 */
interface IterProvider
{
    /**
     * @return \Ginq\Iterator\ZeroIterator
     */
    public function zero();

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Ginq\Iterator\RangeIterator
     */
    public function range($start, $stop, $step);

    /**
     * @param int $start
     * @param int $step
     * @return \Ginq\Iterator\RangeInfIterator
     */
    public function rangeInf($start, $step);

    /**
     * @param mixed $x
     * @return \Ginq\Iterator\RepeatIterator
     */
    public function repeat($x);

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\CycleIterator
     */
    public function cycle($xs);

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\RenumIterator
     */
    public function renum($xs);

    /**
     * @param \Iterator $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Ginq\Iterator\SelectIterator
     */
    public function select($xs, $valueSelector, $keySelector);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\WhereIterator
     */
    public function where($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @return \Ginq\Iterator\ReverseIterator
     */
    public function reverse($xs);

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Ginq\Iterator\TakeIterator
     */
    public function take($xs, $n);

    /**
     * @param \Iterator $xs
     * @param int $n
     * @return \Ginq\Iterator\DropIterator
     */
    public function drop($xs, $n);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\TakeWhileIterator
     */
    public function takeWhile($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @param Predicate $predicate
     * @return \Ginq\Iterator\DropWhileIterator
     */
    public function dropWhile($xs, $predicate);

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @return \Ginq\Iterator\ConcatIterator
     */
    public function concat($xs, $ys);

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @return \Ginq\Iterator\SelectManyIterator
     */
    public function selectMany($xs, $manySelector);

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Ginq\Iterator\SelectManyWithJoinIterator
     */
    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     * @return \Ginq\Iterator\ZipIterator
     */
    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector);

    /**
     * @param \Iterator $xs
     * @param Selector $groupingKeySelector
     * @param Selector $elementSelector
     * @param Selector $groupSelector
     * @return \Ginq\Iterator\GroupByIterator
     */
    public function groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector);
}

