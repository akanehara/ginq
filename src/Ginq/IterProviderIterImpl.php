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
class Ginq_IterProviderGinqIterImpl implements Ginq_IterProvider
{

    public function zero()
    {
        return new Ginq_Iterator_ZeroIterator();
    }

    public function range($start, $stop, $step)
    {
        return new Ginq_Iterator_RangeIterator($start, $stop, $step);
    }

    public function rangeInf($start, $step)
    {
        return new Ginq_Iterator_RangeInfIterator($start, $step);
    }

    public function repeat($x)
    {
        return new Ginq_Iterator_RepeatIterator($x);
    }

    public function cycle($xs)
    {
        return new Ginq_Iterator_CycleIterator($xs);
    }

    public function select($xs, $selector, $keySelector)
    {
        return new Ginq_Iterator_SelectIterator($xs, $selector, $keySelector);
    }

    public function where($xs, $predicate)
    {
        return new Ginq_Iterator_WhereIterator($xs, $predicate);
    }

    public function take($xs, $n)
    {
        return new Ginq_Iterator_TakeIterator($xs, $n);
    }

    public function drop($xs, $n)
    {
        return new Ginq_Iterator_DropIterator($xs, $n);
    }

    public function takeWhile($xs, $predicate)
    {
        return new Ginq_Iterator_TakeWhileIterator($xs, $predicate);
    }

    public function dropWhile($xs, $predicate)
    {
        return new Ginq_Iterator_DropWhileIterator($xs, $predicate);
    }

    public function concat($xs, $ys)
    {
        return new Ginq_Iterator_ConcatIterator($xs, $ys);
    }

    public function selectMany($xs, $manySelector)
    {
        return new Ginq_Iterator_SelectManyIterator($xs, $manySelector);
    }

    public function selectManyWithJoin($xs, $manySelector, $joinSelector)
    {
        return new Ginq_Iterator_SelectManyWithJoinIterator($xs, $manySelector, $joinSelector);
    }

    public function zip($xs, $ys, $joinSelector)
    {
        return new Ginq_Iterator_ZipIterator($xs, $ys, $joinSelector);
    }

    public function groupBy($xs, $keySelector, $elementSelector, $groupSelector)
    {
        return new Ginq_Iterator_GroupByIterator($xs, $keySelector, $elementSelector, $groupSelector);
    }

}

