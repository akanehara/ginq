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

require_once dirname(__FILE__) . "/iter.php";

require_once dirname(__FILE__) . "/IterProvider.php";
require_once dirname(__FILE__) . "/Lookup.php";

/**
 * IterProviderIterImpl
 * @package Ginq
 */
class IterProviderIterImpl implements IterProvider
{

    public function zero()
    {
        require_once dirname(__FILE__) . "/Iterator/ZeroIterator.php";
        return new ZeroIterator();
    }

    public function range($start, $stop, $step)
    {
        require_once dirname(__FILE__) . "/Iterator/RangeIterator.php";
        return new RangeIterator($start, $stop, $step);
    }

    public function rangeInf($start, $step)
    {
        require_once dirname(__FILE__) . "/Iterator/RangeInfIterator.php";
        return new RangeInfIterator($start, $step);
    }

    public function repeat($x)
    {
        require_once dirname(__FILE__) . "/Iterator/RepeatIterator.php";
        return new RepeatIterator($x);
    }

    public function cycle($xs)
    {
        require_once dirname(__FILE__) . "/Iterator/CycleIterator.php";
        return new CycleIterator($xs);
    }

    public function sequence($xs)
    {
        require_once dirname(__FILE__) . "/Iterator/SequenceIterator.php";
        return new SequenceIterator($xs);
    }

    public function select($xs, $selector, $keySelector)
    {
        require_once dirname(__FILE__) . "/Iterator/SelectIterator.php";
        return new SelectIterator($xs, $selector, $keySelector);
    }

    public function where($xs, $predicate)
    {
        require_once dirname(__FILE__) . "/Iterator/WhereIterator.php";
        return new WhereIterator($xs, $predicate);
    }

    public function reverse($xs)
    {
        require_once dirname(__FILE__) . "/Iterator/ReverseIterator.php";
        return new ReverseIterator($xs, $predicate);
    }

    public function take($xs, $n)
    {
        require_once dirname(__FILE__) . "/Iterator/TakeIterator.php";
        return new TakeIterator($xs, $n);
    }

    public function drop($xs, $n)
    {
        require_once dirname(__FILE__) . "/Iterator/DropIterator.php";
        return new DropIterator($xs, $n);
    }

    public function takeWhile($xs, $predicate)
    {
        require_once dirname(__FILE__) . "/Iterator/TakeWhileIterator.php";
        return new TakeWhileIterator($xs, $predicate);
    }

    public function dropWhile($xs, $predicate)
    {
        require_once dirname(__FILE__) . "/Iterator/DropWhileIterator.php";
        return new DropWhileIterator($xs, $predicate);
    }

    public function concat($xs, $ys)
    {
        require_once dirname(__FILE__) . "/Iterator/ConcatIterator.php";
        return new ConcatIterator($xs, $ys);
    }

    public function selectMany($xs, $manySelector)
    {
        require_once dirname(__FILE__) . "/Iterator/SelectManyIterator.php";
        return new SelectManyIterator($xs, $manySelector);
    }

    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        require_once dirname(__FILE__) . "/Iterator/SelectManyWithJoinIterator.php";
        return new SelectManyWithJoinIterator($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);
    }

    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        require_once dirname(__FILE__) . "/Iterator/ZipIterator.php";
        return new ZipIterator($xs, $ys, $valueJoinSelector, $keyJoinSelector);
    }

    public function groupBy($xs, $keySelector, $elementSelector, $groupSelector)
    {
        require_once dirname(__FILE__) . "/Iterator/GroupByIterator.php";
        return new GroupByIterator($xs, $keySelector, $elementSelector, $groupSelector);
    }

}

