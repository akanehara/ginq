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

require_once dirname(__FILE__) . "/Ginq/iter.php";
require_once dirname(__FILE__) . "/Ginq/Lookup.php";

/**
 * Ginq
 * @package Ginq
 */
class Ginq implements IteratorAggregate
{
    protected $it = null;

    public static $gen = null;

    public static function useIterator() {
        require_once dirname(__FILE__) . "/Ginq/IterProviderIterImpl.php";
        self::$gen = new IterProviderIterImpl();
    }

    public static function useGenerator() {
        require_once dirname(__FILE__) . "/Ginq/IterProviderGenImpl.php";
        self::$gen = new IterProviderGenImpl();
    }

    protected function __construct($it)
    {
        $this->it = $it;
    }

    public function getIterator()
    {
        return $this->it;
    }

    public function toArray()
    {
        $arr = array();
        foreach ($this->it as $x) {
            array_push($arr, $x);
        }
        return $arr;
    }

    public function toArrayRec()
    {
        $arr = array();
        foreach ($this->it as $x) {
            if ($x instanceof Iterator || $x instanceof IteratorAggregate) {
                $x = Ginq::from($x)->toArray();
            }
            array_push($arr, $x);
        }
        return $arr;
    }
    public function any($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $x) {
            if ($p($x) == true) {
                return true;
            }
        }
        return false;
    }

    public function all($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $x) {
            if ($p($x) == false) {
                return false;
            }
        }
        return true;
    }

    public function fold($accumulator, $operator) {
        $acc = $accumulator;
        foreach ($this->it as $x) {
            $acc = $operator($acc, $x);
        }
    }

    public static function zero()
    {
        return self::from(self::$gen->zero());
    }

    public static function range($start, $stop = null, $step = 1)
    {
        if (!is_numeric($start)) {
            throw new InvalidArgumentException(
                "self::range() numeric start arguments expected.");
        }
        if (is_null($stop)) {
            return self::from(self::$gen->rangeInf($start, $step));
        } else {
            return self::from(self::$gen->range($start, $stop, $step));
        }
    }

    public static function repeat($x)
    {
        return self::from(self::$gen->repeat($x));
    }

    public static function cycle($xs)
    {
        return self::from(self::$gen->cycle(Ginq::from($xs)));
    }

    public static function from($xs)
    {
        if ($xs instanceof Ginq) {
            return $xs;
        } else {
            return new Ginq(iter($xs));
        }
    }

    public function select($selector)
    {
        return self::from(self::$gen->select(
            $this->it,
            self::_parse_selector($selector)
        ));
    }

    public function where($predicate)
    {
        return self::from(self::$gen->where(
            $this->it,
            self::_parse_predicate($predicate)
        ));
    }

    public function take($n)
    {
        return self::from(self::$gen->take($this->it, $n));
    }

    public function drop($n)
    {
        return self::from(self::$gen->drop($this->it, $n));
    }

    public function takeWhile($predicate)
    {
        return self::from(self::$gen->takeWhile(
            $this->it,
            self::_parse_predicate($predicate)
        ));
    }

    public function dropWhile($predicate)
    {
        return self::from(self::$gen->dropWhile($this->it, self::_parse_predicate($predicate)));
    }

    public function concat($rhs)
    {
        return Ginq::from(self::$gen->concat(
            $this->it, Ginq::from($rhs)
        ));
    }

    public function selectMany($manySelector, $joinSelector = null)
    {
        if (is_null($joinSelector)) {
            return self::from(self::$gen->selectMany(
                $this->it,
                self::_parse_selector($manySelector)));
        } else {
            return self::from(self::$gen->selectManyWithJoin(
                $this->it,
                self::_parse_selector($manySelector),
                self::_parse_join_selector($joinSelector)));
        }
    }

    public function join(
        $inner, $outerKeySelector, $innerKeySelector, $joinSelector)
    {
        $outerKeySelector = self::_parse_selector($outerKeySelector);
        $innerKeySelector = self::_parse_selector($innerKeySelector);
        $innerLookup = Lookup::from($inner, $innerKeySelector);
        return $this->selectMany(
            function($outer) use ($innerLookup, $outerKeySelector) {
                return $innerLookup->get($outerKeySelector($outer));
            },
            $joinSelector
        );
    }

    public function zip($rhs, $joinSelector)
    {
        return self::from(self::$gen->zip(
            $this->it,
            self::from($rhs),
            self::_parse_join_selector($joinSelector)));
    }

    public function groupBy($keySelector, $elementSelector = null)
    {
        if (is_null($elementSelector)) {
            $elementSelector = function($x) { return $x; };
        }
        return self::from(self::$gen->groupBy(
            $this->it,
            self::_parse_selector($keySelector),
            self::_parse_selector($elementSelector)
        ));
    }

    protected static function _parse_selector($selector)
    {
        if (is_string($selector)) {
            return function($x) use ($selector) {
                if (is_array($x)) {
                    return @$x[$selector];
                } else if (is_object($x)) {
                    return @$x->{$selector};
                } else {
                    $type = gettype($x);
                    throw new DomainException("'$type' object has no key or field"); 
                }
            };
        } else if (is_callable($selector)) {
            return $selector;
        } else {
            $type = gettype($selector);
            throw new InvalidArgumentException(
                "'selector' string or callable expected, got $type");
        }
    }

    protected static function _parse_join_selector($joinSelector)
    {
        if (is_callable($joinSelector)) {
            return $joinSelector;
        } else {
            $type = gettype($joinSelector);
            throw new InvalidArgumentException(
                "'join selector' callable (2 arguments) expected, got $type");
        }
    }

    protected static function _parse_predicate($predicate)
    {
        if (is_string($predicate)) {
            return function($x) use ($predicate) {
                if (is_array($x)) {
                    return @$x[$predicate];
                } else if (is_object($x)) {
                    return @$x->{$predicate};
                } else {
                    $type = gettype($x);
                    throw new DomainException("'$type' object has no key or field"); 
                }
            };
        } else if (is_callable($predicate)) {
            return $predicate;
        } else {
            $type = gettype($predicate);
            throw new InvalidArgumentException(
                "'predicate' callable expected, got $type");
        }
    }

}

if (class_exists("Generator")) {
    Ginq::useGenerator();
} else {
    Ginq::useIterator();
}

