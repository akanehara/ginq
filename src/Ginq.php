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

require_once __DIR__ . "/Ginq/iter.php";
require_once __DIR__ . "/Ginq/selector.php";

/**
 * Ginq
 *
 * @package Ginq
 */
class Ginq implements IteratorAggregate
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
        self::$gen = new Ginq\IterProviderIterImpl();
    }

    public static function useGenerator() {
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
     * @deprecated Alias method to rehash().
     *
     * @param string|callable      $valueSelector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
    public function sequence($valueSelector, $keySelector = null)
    {
        return $this->rehash($valueSelector, $keySelector);
    }

    /**
     * Alias method to select().
     *
     * @param string|callable      $valueSelector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
    public function map($valueSelector, $keySelector = null)
    {
        return $this->select($valueSelector, $keySelector);
    }

    /**
     * Alias method to where().
     *
     * @param string|callable $predicate ($value, $key)
     * @return Ginq
     */
    public function filter($predicate)
    {
        return $this->where($predicate);
    } 

    /**
     * Alias method to filter().
     *
     * @param string|callable      $valueSelector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
    public function collect($valueSelector, $keySelector = null)
    {
        return $this->filter($valueSelector, $keySelector);
    }

    /**
     * Alias method to foldLeft().
     *
     * @param mixed    $accumulator
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function fold($accumulator, $operator)
    {
        return $this->foldLeft($accumulator, $operator);
    }

    /**
     * Alias method to reduceLeft().
     *
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function reduce($operator)
    {
        return $this->reduceLeft($operator);
    }

    /**
     * Alias method to contains().
     *
     * @param mixed $value
     * @return bool
     */
    public function has($value)
    {
        return $this->contains($value);
    } 

    /**
     * Alias method to containsKey().
     *
     * @param mixed $key
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->containsKey($key);
    }

    /**
     * Alias method to first().
     *
     * @param mixed $default
     * @return mixed
     */
    public function head($default = null)
    {
        return $this->first($default);
    }

    /**
     * Alias method to rest().
     *
     * @param array|Traversable $default
     * @return Ginq
     */
    public function tail($default = array())
    {
        return $this->rest($default);
    }

    /**
     * Alias method to drop().
     *
     * @param int $n
     * @return Ginq
     */
    public function skip($n)
    {
        return $this->drop($n);
    }

    /**
     * Alias method to dropWhile().
     *
     * @param string|callable $predicate
     * @return Ginq
     */
    public function skipWhile($predicate)
    {
        return $this->dropWhile($predicate);
    }

    // methods.

    /**
     * @param int $start
     * @return callable
     */
    public static function seq($start = 0)
    {
        return Ginq\seq($start);
    }

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
        foreach ($this->it as $k => $v) {
            $arr[$k] = $v;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function toArrayRec()
    {
        $arr = array();
        foreach ($this->it as $k => $v) {
            if ($v instanceof \Iterator || $v instanceof \IteratorAggregate) {
                $arr[$k] = self::from($v)->toArrayRec();
            } else {
                $arr[$k] = $v;
            }
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function toAssoc()
    {
        $alist = array();
        foreach ($this->it as $k => $v) {
            array_push($alist, array($k, $v));
        }
        return $alist;
    }

    /**
     * @return array
     */
    public function toAssocRec()
    {
        $alist = array();
        foreach ($this->it as $k => $v) {
            if ($v instanceof \Iterator || $v instanceof \IteratorAggregate) {
                array_push($alist, array($k, self::from($v)->toAssocRec()));
            } else {
                array_push($alist, array($k, $v));
            }
        }
        return $alist;
    }

    /**
     * @param callable|null $keySelector   ($value, $key)
     * @param callable|null $valueSelector ($value, $key)
     * @return array
     */
    public function toDictionary($keySelector = null, $valueSelector = null)
    {
        $keySelector = Ginq::_parse_selector($keySelector);
        if (is_null($valueSelector)) {
            $valueSelector = function($v, $k) { return $v; };
        } else {
            $valueSelector = Ginq::_parse_selector($valueSelector);
        }
        if (is_null($keySelector)) {
            $keySelector = function($v, $k) { return $k; };
        } else {
            $keySelector = Ginq::_parse_selector($keySelector);
        }
        return $this->fold(
            array(),
            function($acc, $v0, $k0) use ($keySelector, $valueSelector) {
                $k1 = $keySelector($v0, $k0);
                $v1 = $valueSelector($v0, $k0);
                if ($v1 instanceof \Iterator || $v1 instanceof \IteratorAggregate) {
                    $v1 = Ginq::from($v1)->toArrayRec();
                }
                $acc[$k1] = $v1;
                return $acc;
            }
        );
    }

    /**
     * @param callable      $combiner      ($exist, $value, $key)
     * @param callable|null $keySelector   ($value, $kay)
     * @param callable|null $valueSelector ($value, $key)
     * @return array
     */
    public function toDictionaryWith($combiner, $keySelector = null, $valueSelector = null)
    {
        if (is_null($valueSelector)) {
            $valueSelector = function($v, $k) { return $v; };
        } else {
            $valueSelector = Ginq::_parse_selector($valueSelector);
        }
        if (is_null($keySelector)) {
            $keySelector = function($v, $k) { return $k; };
        } else {
            $keySelector = Ginq::_parse_selector($keySelector);
        }
        return $this->fold(
            array(),
            function($acc, $v0, $k0) use ($combiner, $keySelector, $valueSelector) {
                $k1 = $keySelector($v0, $k0);
                $v1 = $valueSelector($v0, $k0);
                if ($v1 instanceof \Iterator || $v1 instanceof \IteratorAggregate) {
                    $v1 = Ginq::from($v1)->toArrayRec();
                }
                if (key_exists($k1, $acc)) {
                    $acc[$k1] = $combiner($acc[$k1], $v1, $k1);
                } else {
                    $acc[$k1] = $v1;
                }
                return $acc;
            }
        );
    }

    /**
     * @param string|callable $predicate ($value, $key)
     * @return bool
     */
    public function any($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $k => $v) {
            if ($p($v, $k) == true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string|callable $predicate ($value, $key)
     * @return bool
     */
    public function all($predicate)
    {
        $p = self::_parse_predicate($predicate);
        foreach ($this->it as $k => $v) {
            if ($p($v, $k) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function first($default = null)
    {
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
    public function rest($default = array())
    {
        $this->it->rewind();
        if ($this->it->valid()) {
            return $this->drop(1);
        } else {
            return self::from($default);
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value)
    {
        return $this->any(
            function($v, $k) use ($value) {
                return $v == $value;
            }
        );
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function containsKey($key)
    {
        return $this->any(
            function($v, $k) use ($key) {
                return $k == $key;
            }
        );
    }

    /**
     * @param callable $predicate ($value, $key)
     * @param mixed $default
     * @return mixed
     */
    public function find($predicate, $default = null)
    {
        foreach ($this->it as $k => $v) {
            if ($predicate($v, $k)) {
                return $v;
            }
        }
        return $default;
    }

    /**
     * @param mixed $accumulator
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function foldLeft($accumulator, $operator)
    {
        $acc = $accumulator;
        foreach ($this as $k => $v) {
            $acc = $operator($acc, $v, $k);
        }
        return $acc;
    }

    /**
     * @param mixed $accumulator
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function foldRight($accumulator, $operator)
    {
        $acc = $accumulator;
        foreach ($this->reverse() as $k => $v) {
            $acc = $operator($acc, $v, $k);
        }
        return $acc;
    }

    /**
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function reduceLeft($operator)
    {
        $it = $this->it;
        $it->rewind();
        if (!$it->valid()) {
            throw new LengthException("reduce of empty sequence");
        }
        $acc = $it->current();
        $it->next();
        while ($it->valid()) {
            $acc = $operator($acc, $it->current(), $it->key());
            $it->next();
        }
        return $acc;
    }

    /**
     * @param callable $operator ($accumulator, $value, $key)
     * @return mixed
     */
    public function reduceRight($operator)
    {
        $it = $this->reverse()->it;
        $it->rewind();
        if (!$it->valid()) {
            throw new LengthException("reduce of empty sequence");
        }
        $acc = $it->current();
        $it->next();
        while ($it->valid()) {
            $acc = $operator($acc, $it->current(), $it->key());
            $it->next();
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
                "Ginq::range() numeric start arguments expected.");
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
     * @return Ginq
     */
    public function rehash()
    {
        return self::from(self::$gen->rehash($this->it));
    }

    /**
     * @param string|callable $selector
     * @param string|callable|null $keySelector
     * @return Ginq
     */
    public function select($selector, $keySelector = null)
    {
        if (is_null($keySelector)) {
            $keySelector = function($v, $k) { return $k; };
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
     * @return Ginq
     */
    public function reverse()
    {
        return self::from(self::$gen->reverse($this->it));
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
     * @return Ginq
     */
    public function selectMany($manySelector)
    {
        return self::from(self::$gen->selectMany(
            $this->it,
            self::_parse_selector($manySelector)));
    }

    /**
     * @param string|callable $manySelector
     * @param callable        $valueJoinSelector
     * @param callable|null   $keyJoinSelector
     * @return Ginq
     */
    public function selectManyWith($manySelector, $valueJoinSelector, $keyJoinSelector = null)
    {
        if (is_null($keyJoinSelector)) {
            $keyJoinSelector = function($v0, $k0, $v1, $k1) { return $k1; };
        } else {
            $keyJoinSelector = self::_parse_join_selector($keyJoinSelector);
        }
        return self::from(self::$gen->selectManyWithJoin(
            $this->it,
            self::_parse_selector($manySelector),
            self::_parse_join_selector($valueJoinSelector),
            $keyJoinSelector
        ));
    }

    /**
     * @param array|Traversable $inner
     * @param string|callable      $outerKeySelector
     * @param string|callable      $innerKeySelector
     * @param string|callable      $valueJoinSelector
     * @param string|callable|null $keyJoinSelector
     * @return Ginq
     */
    public function join(
        $inner, $outerKeySelector, $innerKeySelector, $valueJoinSelector, $keyJoinSelector = null)
    {
        $outerKeySelector = self::_parse_selector($outerKeySelector);
        $innerKeySelector = self::_parse_selector($innerKeySelector);
        if (is_null($keyJoinSelector)) {
            $keyJoinSelector = function($v0, $v1, $k0, $k1) { return $k0; };
        } else {
            $keyJoinSelector = self::_parse_join_selector($keyJoinSelector);
        }
        $innerLookup = Ginq\Lookup::from($inner, $innerKeySelector);
        return $this->selectManyWith(
            function($outer, $outerKey) use ($innerLookup, $outerKeySelector) {
                return $innerLookup->get(
                    $outerKeySelector($outer, $outerKey)
                );
            },
            self::_parse_join_selector($valueJoinSelector),
            $keyJoinSelector
        );
    }

    /**
     * @param array|Traversable    $rhs
     * @param string|callable      $valueJoinSelector
     * @param string|callable|null $keyJoinSelector
     * @return Ginq
     */
    public function zip($rhs, $valueJoinSelector, $keyJoinSelector = null)
    {
        if (is_null($keyJoinSelector)) {
            $keyJoinSelector = function($v0, $v1, $k0, $k1) { return $k0; };
        } else {
            $keyJoinSelector = self::_parse_join_selector($keyJoinSelector);
        }
        return self::from(self::$gen->zip(
            $this->it,
            Ginq\iter($rhs),
            self::_parse_join_selector($valueJoinSelector),
            $keyJoinSelector
        ));
    }

    /**
     * @param string|callable      $groupingKeySelector
     * @param string|callable|null $elementSelector
     * @return Ginq
     */
    public function groupBy($groupingKeySelector, $elementSelector = null)
    {
        if (is_null($elementSelector)) {
            $elementSelector = function($v, $k) { return $v; };
        } else {
            $elementSelector = self::_parse_selector($elementSelector);
        }
        return self::from(self::$gen->groupBy(
            $this->it,
            self::_parse_selector($groupingKeySelector),
            $elementSelector,
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
            return function($v, $k) use ($selector) {
                if (is_array($v)) {
                    return @$v[$selector];
                } else if (is_object($v)) {
                    return @$v->{$selector};
                } else {
                    $type = gettype($v);
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
            return function($v, $k) use ($predicate) {
                if (is_array($v)) {
                    return @$v[$predicate];
                } else if (is_object($v)) {
                    return @$v->{$predicate};
                } else {
                    $type = gettype($v);
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

    public function __call($name, $args) {
        if (isset(self::$registeredFunctions[$name])) {
            call_user_func_array(
                self::$registeredFunctions[$name], 
                array_merge(array($this), $args)
            );
        }
    }

    private static $registeredFunctions = array();

    public static function register($className) {
        $ref = new \ReflectionClass($className);

        $funcNames = Ginq::from($ref->getMethods(ReflectionMethod::IS_STATIC))
            ->where(function ($m) { return $m->isPublic(); })
            ->where(function ($m) {
                $p = Ginq::from($m->getParameters())->first(false);
                if ($p === false) return false;

                $c = $p->getClass();

                return ($c->getName() === 'Ginq') or $c->isSubclassOf('Ginq');
            })
            ->select(function ($m) { return $m->getName(); })
            ->toArray()
        ;

        foreach ($funcNames as $func) {
            self::$registeredFunctions[$func] = array($className, $func);
        }
    }

    public static function listRegisterdFunctions() {
        return self::$registeredFunctions;
    }

    public static function _registerAutoloadFunction() {
        spl_autoload_register(function($class) {
            $class = ltrim($class, '\\');
            if ($class == 'Ginq') {
                include __DIR__ . DIRECTORY_SEPARATOR . 'Ginq.php';
                return; // Ginq class itself.
            }
            $pos = strpos($class, '\\');
            if ($pos === false) {
                return; // Not in namespace.
            }
            $rootNS = substr($class, 0, $pos);
            if ($rootNS != 'Ginq') {
                return; // Not in Ginq namespace.
            }
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            include __DIR__ . DIRECTORY_SEPARATOR . $classPath;
        });
    }
}

Ginq::_registerAutoloadFunction();
if (class_exists("Generator")) {
    Ginq::useGenerator();
} else {
    Ginq::useIterator();
}
