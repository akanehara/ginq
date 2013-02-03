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

$dir = dirname(__FILE__);

require_once $dir . "/iter.php";

require_once $dir . "/IterProvider.php";
require_once $dir . "/Lookup.php";

require_once $dir . "/Iterator/ZeroIterator.php";
require_once $dir . "/Iterator/RangeIterator.php";
require_once $dir . "/Iterator/RangeInfIterator.php";
require_once $dir . "/Iterator/RepeatIterator.php";
require_once $dir . "/Iterator/CycleIterator.php";
require_once $dir . "/Iterator/SelectIterator.php";
require_once $dir . "/Iterator/WhereIterator.php";
require_once $dir . "/Iterator/TakeIterator.php";
require_once $dir . "/Iterator/DropIterator.php";
require_once $dir . "/Iterator/TakeWhileIterator.php";
require_once $dir . "/Iterator/DropWhileIterator.php";
require_once $dir . "/Iterator/ConcatIterator.php";
require_once $dir . "/Iterator/SelectManyIterator.php";
require_once $dir . "/Iterator/SelectManyWithJoinIterator.php";
require_once $dir . "/Iterator/ZipIterator.php";
require_once $dir . "/Iterator/GroupByIterator.php";


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

    public function select($xs, $selector, $keySelector)
    {
        return new SelectIterator($xs, $selector, $keySelector);
    }

    public function where($xs, $predicate)
    {
        return new WhereIterator($xs, $predicate);
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

    public function selectManyWithJoin($xs, $manySelector, $joinSelector)
    {
        return new SelectManyWithJoinIterator($xs, $manySelector, $joinSelector);
    }

    public function zip($xs, $ys, $joinSelector)
    {
        return new ZipIterator($xs, $ys, $joinSelector);
    }

    public function groupBy($xs, $keySelector, $elementSelector, $groupSelector)
    {
        return new GroupByIterator($xs, $keySelector, $elementSelector, $groupSelector);
    }

}

