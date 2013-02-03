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

    /**
     * aliases.
     */

    public function map($selector, $keySelector = null) {
        return $this->select($selector, $keySelector);
    }

    public function filter($predicate) { return $this->where($predicate); }

    public function elem($element) { return $this->contains($element); }

    public function head($default = null) { return $this->first($default); }

    public function tail($default = array()) { return $this->rest($default); }

    public function reduce($accumulator, $operator) { return $this->fold($accumulator, $operator); }

    public function skip($n) { return $this->drop($n); }

    public function skipWhile($predicate) { return $this->dropWhile($predicate); }

    /**
     * methods.
     */

    public function getIterator()
    {
        return $this->it;
    }

    public function toArray()
    {
        $arr = array();
        foreach ($this->it as $k => $x) {
            $arr[$k] = $x;
        }
        return $arr;
    }

    public function toArrayRec()
    {
        $arr = array();
        foreach ($this->it as $k => $x) {
            if ($x instanceof Iterator || $x instanceof IteratorAggregate) {
                $arr[$k] = self::from($x)->toArrayRec();
            } else {
                $arr[$k] = $x;
            }
        }
        return $arr;
    }

    public function toDictionary($keySelector, $elementSelector = null)
    {
        if (is_null($elementSelector)) {
            $elementSelector = function($x, $k) { return $x; };
        }
        return $this->select($elementSelector, $keySelector)->toArray();
    }

    public function any($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $k => $x) {
            if ($p($x, $k) == true) {
                return true;
            }
        }
        return false;
    }

    public function all($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $k => $x) {
            if ($p($x, $k) == false) {
                return false;
            }
        }
        return true;
    }

    public function first($default = null) {
        $this->it->rewind();
        if ($this->it->valid()) {
            return $this->it->current();
        } else {
            return $default;
        }
    }

    public function rest($default = array()) {
        $this->it->rewind();
        if ($this->it->valid()) {
            return $this->drop(1);
        } else {
            return Ginq::from($default);
        }
    }

    public function contains($element) {
        return $this->any(
            function($x, $k) use ($element) {
                return $x == $element;
            }
        );
    }

    public function find($predicate, $default = null) {
        foreach ($this->it as $k => $x) {
            if ($predicate($x, $k)) {
                return $x;
            }
        }
        return $default;
    }

    public function fold($accumulator, $operator) {
        $acc = $accumulator;
        foreach ($this->it as $x) {
            $acc = $operator($acc, $x);
        }
        return $acc;
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

    public static function repeat($element)
    {
        return self::from(self::$gen->repeat($element));
    }

    public static function cycle($xs)
    {
        return self::from(self::$gen->cycle(self::from($xs)));
    }

    public static function from($xs)
    {
        if ($xs instanceof Ginq) {
            return $xs;
        } else {
            return new Ginq(iter($xs));
        }
    }

    public function select($selector, $keySelector = null)
    {
        if (is_null($keySelector)) {
            $keySelector = function($x, $k) { return $k; };
        } else {
            $keySelector = self::_parse_selector($keySelector);
        }
        return self::from(self::$gen->select(
            $this->it,
            self::_parse_selector($selector),
            $keySelector
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
        return self::from(self::$gen->concat(
            $this->it, self::from($rhs)
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
            function($outer, $outerKey) use ($innerLookup, $outerKeySelector) {
                return $innerLookup->get(
                    $outerKeySelector($outer, $outerKey)
                );
            },
            $joinSelector
        );
    }

    public function zip($rhs, $joinSelector)
    {
        return self::from(self::$gen->zip(
            $this->it,
            iter($rhs),
            self::_parse_join_selector($joinSelector))
        );
    }

    public function groupBy($keySelector, $elementSelector = null)
    {
        if (is_null($elementSelector)) {
            $elementSelector = function($x, $k) { return $x; };
        }
        return self::from(self::$gen->groupBy(
            $this->it,
            self::_parse_selector($keySelector),
            self::_parse_selector($elementSelector),
            function ($xs, $k) { return Ginq::from($xs); }
        ));
    }

    protected static function _parse_selector($selector)
    {
        if (is_string($selector)) {
            return function($x, $k) use ($selector) {
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
            return function($x, $k) use ($predicate) {
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

