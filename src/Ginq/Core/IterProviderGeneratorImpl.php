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
use Ginq\Ginq;
use Ginq\GroupingGinq;
use Ginq\Iterator\MemoizeIterator;
use Ginq\Selector\DelegateSelector;
use Ginq\Selector\ValueSelector;
use Ginq\Util\IteratorUtil;

/**
 * IterProviderGeneratorImpl
 * @package Ginq
 */
class IterProviderGeneratorImpl implements IterProvider
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
     * @return \Iterator
     */
    public function zero()
    {
        while (false) yield null;
    }

    /**
     * @param mixed|\Closure $seed
     * @param \Closure $generator seed -> ([v, seed] | null)
     * @throws \RuntimeException
     * @return \Iterator
     */
    public function unfold($seed, $generator)
    {
        while (true) {
            $x = $generator($seed);
            if (empty($x)) break;
            if (!is_array($x)) {
                $type = gettype($x);
                throw new \RuntimeException(
                    "unfold: Generator function returned $type. array (2 elements) or null expected.");
            }
            list($v, $seed) = $x;
            yield $v;
        }
    }

    /**
     * @param int $start
     * @param int $stop
     * @param int $step
     * @return \Iterator
     */
    public function range($start, $stop, $step)
    {
        $x = $start;
        if (0 <= $step) {
            while ($x <= $stop) {
                yield $x;
                $x += $step;
            }
        } else {
            while ($stop <= $x) {
                yield $x;
                $x += $step;
            }
        }
    }

    /**
     * @param int $start
     * @param int $step
     * @return \Iterator
     */
    public function rangeInf($start, $step)
    {
        $x = $start;
        if (0 <= $step) {
            while (true) {
                yield $x;
                $x += $step;
            }
        } else {
            while (true) {
                yield $x;
                $x += $step;
            }
        }
    }

    /**
     * @param mixed $x
     * @param int|null $count
     * @return \Iterator
     */
    public function repeat($x, $count)
    {
        if (is_null($count)) {
            while (true) {
                yield $x;
            }
        } else {
            for ($i=0; $i<$count; $i++) yield $x;
        }
    }

    /**
     * @param /Closure $valueFactory
     * @param int|null $count
     * @return \Iterator
     */
    public function lazyRepeat($valueFactory, $count)
    {
        $x = $valueFactory();
        if (is_null($count)) {
            while (true) {
                yield $x;
            }
        } else {
            for ($i = 0; $i < $count; $i++) yield $x;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function cycle($xs)
    {
        while (true) {
            foreach($xs as $k => $v) {
                yield $k => $v;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function renum($xs)
    {
        $i = 0;
        foreach($xs as $k => $v) {
            yield $i++ => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param \Closure $fn
     * @return \Iterator
     */
    public function each($xs, $fn)
    {
        foreach($xs as $k => $v) {
            $fn($v, $k);
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     * @return \Iterator
     */
    public function select($xs, $valueSelector, $keySelector)
    {
        foreach($xs as $k => $v) {
            yield $keySelector->select($v, $k) => $valueSelector->select($v, $k);
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function where($xs, $predicate)
    {
        foreach($xs as $k => $v) {
            if ($predicate->predicate($v, $k)) {
                yield $k => $v;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     */
    public function reverse($xs)
    {
        $ys = array();
        foreach ($xs as $k => $v) {
            $ys[] = array($k, $v);
        }
        $rs = array_reverse($ys);
        foreach ($rs as $y) {
            @list($k, $v) = $y;
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Iterator
     */
    public function take($xs, $n)
    {
        $i = 1;
        foreach($xs as $k => $v) {
            if ($n < $i++) break;
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param int $n
     * @return \Iterator
     */
    public function drop($xs, $n)
    {
        $i = 1;
        foreach($xs as $k => $v) {
            if ($n < $i++) yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function takeWhile($xs, $predicate)
    {
        foreach($xs as $k => $v) {
            if (!$predicate->predicate($v, $k)) break;
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Predicate $predicate
     * @return \Iterator
     */
    public function dropWhile($xs, $predicate)
    {
        $drop = true;
        foreach($xs as $k => $v) {
            if ($drop = $drop && $predicate->predicate($v, $k)) continue;
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @return \Iterator
     */
    public function concat($xs, $ys)
    {
        foreach($xs as $k => $v) yield $k => $v;
        foreach($ys as $k => $v) yield $k => $v;
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @return \Iterator
     */
    public function selectMany($xs, $manySelector)
    {
        foreach($xs as $k0 => $v0) {
            $ys = $manySelector->select($v0, $k0);
            foreach($ys as $k1 => $v1) {
                yield $k1 => $v1;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $manySelector
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Iterator
     */
    public function selectManyWithJoin($xs, $manySelector, $resultValueSelector, $resultKeySelector)
    {
        foreach($xs as $k0 => $v0) {
            $ys = $manySelector->select($v0, $k0);
            foreach($ys as $k1 => $v1) {
                yield
                    $resultKeySelector->select($v0, $v1, $k0, $k1)
                    =>
                    $resultValueSelector->select($v0, $v1, $k0, $k1);
            }
        }
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
    public function join(
            $outer, $inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector,
            $eqComparer)
    {
        $lookup = Ginq::from($inner)
            ->toLookup(
                $innerCompareKeySelector,
                ValueSelector::getInstance(),
                $eqComparer
            );
        return self::selectManyWithJoin(
            $outer,
            new DelegateSelector(
                function ($v, $k) use ($lookup, $outerCompareKeySelector) {
                    return $lookup->get($outerCompareKeySelector->select($v, $k));
                }
            ),
            $resultValueSelector,
            $resultKeySelector
        );
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
        $lookup = Ginq::from($inner)
            ->toLookup(
                $innerCompareKeySelector,
                ValueSelector::getInstance()
            );
        foreach($outer as $k0 => $v0) {
            $compareKey = $outerCompareKeySelector->select($v0, $k0);
            $inners = $lookup->get($compareKey);
            $inners = new GroupingGinq(IteratorUtil::iterator($inners), $compareKey);
            yield  $k0 => $resultValueSelector->select($v0, $inners, $k0, $compareKey);
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param JoinSelector $resultValueSelector
     * @param JoinSelector $resultKeySelector
     * @return \Iterator
     */
    public function zip($xs, $ys, $resultValueSelector, $resultKeySelector)
    {
        $xs = IteratorUtil::iterator($xs);
        $ys = IteratorUtil::iterator($ys);
        $xs->rewind();
        $ys->rewind();
        while ($xs->valid() && $ys->valid()) {
            $xk = $xs->key(); $xv = $xs->current();
            $yk = $ys->key(); $yv = $ys->current();
            yield $resultKeySelector->select($xv, $yv, $xk, $yk) => $resultValueSelector->select($xv, $yv, $xk, $yk);
            $xs->next();
            $ys->next();
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Selector $compareKeySelector
     * @param Selector $elementSelector
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function groupBy($xs, $compareKeySelector, $elementSelector, $eqComparer)
    {
        /**
         * @var GroupingGinq[] $groups
         */
        $groups = Ginq::from($xs)
            ->toLookup($compareKeySelector, $elementSelector, $eqComparer);
        foreach($groups as $g) {
            yield $g->key() => $g;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param Comparer $comparer
     * @return \Iterator
     */
    public function orderBy($xs, $comparer)
    {
        while (false) yield;
        $sorted = array();
        foreach ($xs as $k => $v) {
            array_push($sorted, array($k, $v));
        }
        usort(
            $sorted,
            function($x, $y) use ($comparer) {
                list($k0, $v0) = $x;
                list($k1, $v1) = $y;
                $cmp = $comparer->compare($v0, $v1, $k0, $k1);
                return $cmp;
            }
        );
        foreach ($sorted as $x) {
            list($k, $v) = $x;
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function distinct($xs, $eqComparer)
    {
        $seen = new Set($eqComparer);
        foreach ($xs as $k => $v) {
            if ($seen->add($v)) {
                yield $k => $v;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function union($xs, $ys, $eqComparer)
    {
        $seen = new Set($eqComparer);
        foreach ($xs as $k => $v) {
            if ($seen->add($v)) {
                yield $k => $v;
            }
        }
        foreach ($ys as $k => $v) {
            if ($seen->add($v)) {
                yield $k => $v;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function intersect($xs, $ys, $eqComparer)
    {
        $potentials = new Set($eqComparer, $ys);
        foreach ($xs as $k => $v) {
            if ($potentials->remove($v)) {
                yield $k => $v;
            }
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param array|\Traversable $ys
     * @param EqualityComparer $eqComparer
     * @return \Iterator
     */
    public function except($xs, $ys, $eqComparer)
    {
        $masked = new Set($eqComparer, $ys);
        foreach ($xs as $k => $v) {
            if ($masked->add($v)) {
                yield $k => $v;
            }
        }
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
        $xs = $sourceFactory();
        foreach ($xs as $k => $v) {
            yield $k => $v;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @return \Iterator
     */
    public function buffer($xs, $chunkSize)
    {
        if ($chunkSize < 1) {
            throw new \InvalidArgumentException("chunkSize must be greater than 0");
        }
        $buffer = array();
        $i = $chunkSize;
        foreach ($xs as $v) {
            $buffer[] = $v;
            if (--$i == 0) {
                yield $buffer;
                $i = $chunkSize;
                $buffer = array();
            }
        }
        if (0 < count($buffer)) {
            yield $buffer;
        }
    }

    /**
     * @param array|\Traversable $xs
     * @param int                $chunkSize
     * @param mixed              $padding
     * @return \Iterator
     */
    public function bufferWithPadding($xs, $chunkSize, $padding)
    {
        $buffer = array();
        $i = $chunkSize;
        foreach ($xs as $v) {
            $buffer[] = $v;
            if (--$i == 0) {
                yield $buffer;
                $i = $chunkSize;
                $buffer = array();
            }
        }
        if ($i < $chunkSize) {
            for (;0 < $i; $i--) {
                $buffer[] = $padding;
            }
            yield $buffer;
        }
    }
}

