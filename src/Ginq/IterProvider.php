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

/**
 * IterProvider
 * @package Ginq
 */
interface IterProvider
{
    public function zero();
    public function range($start, $stop, $step);
    public function rangeInf($start, $step);
    public function repeat($x);
    public function cycle($xs);
    public function select($xs, $valueSelector, $keySelector);
    public function where($xs, $predicate);
    public function reverse($xs);
    public function take($xs, $n);
    public function drop($xs, $n);
    public function takeWhile($xs, $predicate);
    public function dropWhile($xs, $predicate);
    public function concat($xs, $ys);
    public function selectMany($xs, $manySelector);
    public function selectManyWithJoin($xs, $manySelector, $valueJoinSelector, $keyJoinSelector);
    public function zip($xs, $ys, $valueJoinSelector, $keyJoinSelector);
    public function groupBy($xs, $groupingKeySelector, $elementSelector, $groupSelector);
}

