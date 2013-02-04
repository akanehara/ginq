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
 *
 * @package Ginq
 */
class Ginq implements \IteratorAggregate
{
    /**
     * @var array|Traversable
     */
    protected $it;

    /**
     * @var Ginq\IterProvider
     */
    protected static $gen = null;

    public static function useIterator() {
        require_once dirname(__FILE__) . "/Ginq/IterProviderIterImpl.php";
        self::$gen = new Ginq\IterProviderIterImpl();
    }

    public static function useGenerator() {
        require_once dirname(__FILE__) . "/Ginq/IterProviderGenImpl.php";
        self::$gen = new Ginq\IterProviderGenImpl();
    }

    /**
     * Constructor
     *
     * @param array|Iterator $it  Any traversable variable
     */
    protected function __construct($it)
    {
        $this->it = $it;
    }

    // aliases.

    /**
     * Alias method to select().
     *
     * @param string|callable $selector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
    public function map($selector, $keySelector = null) {
        return $this->select($selector, $keySelector);
    }

    /**
     * Alias method to where().
     *
     * @param string|callable $predicate
     * @return Ginq
     */
    public function filter($predicate) { return $this->where($predicate); }

    /**
     * Alias method to contains().
     *
     * @param mixed $element
     * @return bool
     */
    public function elem($element) { return $this->contains($element); }

    /**
     * Alias method to first().
     *
     * @param mixed $default
     * @return mixed
     */
    public function head($default = null) { return $this->first($default); }

    /**
     * Alias method to rest().
     *
     * @param array $default
     * @return Ginq
     */
    public function tail($default = array()) { return $this->rest($default); }

    /**
     * Alias method to fold().
     *
     * @param mixed $accumulator
     * @param $operator
     * @return mixed
     */
    public function reduce($accumulator, $operator) { return $this->fold($accumulator, $operator); }

    /**
     * Alias method to drop().
     *
     * @param int $n
     * @return Ginq
     */
    public function skip($n) { return $this->drop($n); }

    /**
     * Alias method to dropWhile().
     *
     * @param string|callable $predicate
     * @return Ginq
     */
    public function skipWhile($predicate) { return $this->dropWhile($predicate); }

    // methods.

    /**
     * Overridden interface of IteratorAggregate.
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return Ginq\iter($this->it);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arr = array();
        foreach ($this->it as $k => $x) {
            $arr[$k] = $x;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function toArrayRec()
    {
        $arr = array();
        foreach ($this->it as $k => $x) {
            if ($x instanceof \Iterator || $x instanceof \IteratorAggregate) {
                $arr[$k] = self::from($x)->toArrayRec();
            } else {
                $arr[$k] = $x;
            }
        }
        return $arr;
    }

    /**
     * @param callable $keySelector
     * @param callable|null $elementSelector
     * @return array
     */
    public function toDictionary($keySelector, $elementSelector = null)
    {
        if (is_null($elementSelector)) {
            $elementSelector = function($x, $k) { return $x; };
        }
        return $this->select($elementSelector, $keySelector)->toArrayRec();
    }

    /**
     * @param string|callable $predicate
     * @return bool
     */
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

    /**
     * @param string|callable $predicate
     * @return bool
     */
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

    /**
     * @param mixed $default
     * @return mixed
     */
    public function first($default = null) {
        $this->it->rewind();
        if ($this->it->valid()) {
            return $this->it->current();
        } else {
            return $default;
        }
    }

    /**
     * @param array|Traversable $default
     * @return Ginq
     */
    public function rest($default = array()) {
        $this->it->rewind();
        if ($this->it->valid()) {
            return $this->drop(1);
        } else {
            return self::from($default);
        }
    }

    /**
     * @param mixed $element
     * @return bool
     */
    public function contains($element) {
        return $this->any(
            function($x, $k) use ($element) {
                return $x == $element;
            }
        );
    }

    /**
     * @param callable $predicate
     * @param mixed $default
     * @return mixed
     */
    public function find($predicate, $default = null) {
        foreach ($this->it as $k => $x) {
            if ($predicate($x, $k)) {
                return $x;
            }
        }
        return $default;
    }

    /**
     * @param mixed $accumulator
     * @param callable $operator
     * @return mixed
     */
    public function fold($accumulator, $operator) {
        $acc = $accumulator;
        foreach ($this->it as $k => $x) {
            $acc = $operator($acc, $x, $k);
        }
        return $acc;
    }

    /**
     * @return Ginq
     */
    public static function zero()
    {
        return self::from(self::$gen->zero());
    }

    /**
     * @param number $start
     * @param number|null $stop
     * @param number|int $step
     * @throws InvalidArgumentException
     * @return Ginq
     */
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

    /**
     * @param mixed $element
     * @return Ginq
     */
    public static function repeat($element)
    {
        return self::from(self::$gen->repeat($element));
    }

    /**
     * @param array|Traversable $xs
     * @return Ginq
     */
    public static function cycle($xs)
    {
        return self::from(self::$gen->cycle(self::from($xs)));
    }

    /**
     * @param array|Traversable $xs
     * @return Ginq
     */
    public static function from($xs)
    {
        if ($xs instanceof self) {
            return $xs;
        } else {
            return new self(Ginq\iter($xs));
        }
    }

    /**
     * @param string|callable $selector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
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

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function where($predicate)
    {
        return self::from(self::$gen->where(
            $this->it,
            self::_parse_predicate($predicate)
        ));
    }

    /**
     * @param int $n
     * @return Ginq
     */
    public function take($n)
    {
        return self::from(self::$gen->take($this->it, $n));
    }

    /**
     * @param int $n
     * @return Ginq
     */
    public function drop($n)
    {
        return self::from(self::$gen->drop($this->it, $n));
    }

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function takeWhile($predicate)
    {
        return self::from(self::$gen->takeWhile(
            $this->it,
            self::_parse_predicate($predicate)
        ));
    }

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function dropWhile($predicate)
    {
        return self::from(self::$gen->dropWhile($this->it, self::_parse_predicate($predicate)));
    }

    /**
     * @param array|Traversable $rhs
     * @return Ginq
     */
    public function concat($rhs)
    {
        return self::from(self::$gen->concat(
            $this->it, self::from($rhs)
        ));
    }

    /**
     * @param string|callable $manySelector
     * @param callable|null $joinSelector
     * @return Ginq
     */
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

    /**
     * @param array|Traversable $inner
     * @param string|callable $outerKeySelector
     * @param string|callable $innerKeySelector
     * @param string|callable $joinSelector
     * @return Ginq
     */
    public function join(
        $inner, $outerKeySelector, $innerKeySelector, $joinSelector)
    {
        $outerKeySelector = self::_parse_selector($outerKeySelector);
        $innerKeySelector = self::_parse_selector($innerKeySelector);
        $innerLookup = Ginq\Lookup::from($inner, $innerKeySelector);
        return $this->selectMany(
            function($outer, $outerKey) use ($innerLookup, $outerKeySelector) {
                return $innerLookup->get(
                    $outerKeySelector($outer, $outerKey)
                );
            },
            $joinSelector
        );
    }

    /**
     * @param array|Traversable $rhs
     * @param string|callable $joinSelector
     * @return Ginq
     */
    public function zip($rhs, $joinSelector)
    {
        return self::from(self::$gen->zip(
            $this->it,
            Ginq\iter($rhs),
            self::_parse_join_selector($joinSelector))
        );
    }

    /**
     * @param string|callable $keySelector
     * @param string|callable|null $elementSelector
     * @return Ginq
     */
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

    /**
     * @param string|callable $selector
     * @return callable
     * @throws InvalidArgumentException
     * @throws DomainException
     */
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

    /**
     * @param callable $joinSelector
     * @return callable
     * @throws InvalidArgumentException
     */
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

    /**
     * @param string|callable $predicate
     * @return callable
     * @throws InvalidArgumentException
     * @throws DomainException
     */
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

