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

use Ginq\Core\Selector;
use Ginq\Comparer\ComparerParser;
use Ginq\Core\JoinSelector;
use Ginq\Core\Comparer;
use Ginq\Core\EqualityComparer;
use Ginq\EqualityComparer\EqualityComparerParser;
use Ginq\JoinSelector\KeyJoinSelector;
use Ginq\JoinSelector\ValueJoinSelector;
use Ginq\Selector\KeySelector;
use Ginq\Selector\DelegateSelector;
use Ginq\Selector\SelectorParser;
use Ginq\JoinSelector\JoinSelectorParser;
use Ginq\Predicate\PredicateParser;
use Ginq\Selector\ValueSelector;
use Ginq\Util\IteratorUtil;
use Ginq\Comparer\ReverseComparer;
use Ginq\Comparer\ProjectionComparer;
use Ginq\OrderedGinq;
use Ginq\GroupingGinq;

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
     * @var Ginq\Core\IterProvider
     */
    protected static $gen = null;

    public static function useIterator()
    {
        self::$gen = new Ginq\Core\IterProviderIterImpl();
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

    /**
     * default compare
     * @param mixed $x
     * @param mixed $y
     * @return int
     */
    public static function compare($x, $y) {
        return Comparer::getDefault()->compare($x, $y);
    }

    /**
     * default equality comparing
     * @param mixed $x
     * @param mixed $y
     * @return bool
     */
    public static function equals($x, $y) {
        return EqualityComparer::getDefault()->equals($x, $y);
    }

    /**
     * default hash function
     * @param mixed $x
     * @return string
     */
    public static function hash($x) {
        return EqualityComparer::getDefault()->hash($x);
    }

    /**
     * Overridden interface of IteratorAggregate.
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->it;
    }

    /**
     * @param callable|null  $combiner (existV, v, k) -> v
     * @return array
     */
    public function toArray($combiner = null)
    {
        if (is_null($combiner)) {
            return IteratorUtil::toArray($this->getIterator());
        } else {
            return IteratorUtil::toArrayWithCombine($this->getIterator(), $combiner);
        }
    }

    /**
     * @param int|null       $depth
     * @param callable|null  $combiner (existV, v, k) -> v
     * @return array
     */
    public function toArrayRec($depth = null, $combiner = null)
    {
        if (is_null($combiner)) {
            return IteratorUtil::toArrayRec($this->getIterator(), $depth);
        } else {
            return IteratorUtil::toArrayRecWithCombine($this->getIterator(), $depth, $combiner);
        }
    }


    /**
     * @return array
     */
    public function toList()
    {
        return IteratorUtil::toList($this->getIterator());
    }

    /**
     * @param null|int $depth
     * @return array
     */
    public function toListRec($depth = null)
    {
        return IteratorUtil::toListRec($this->getIterator(), $depth);
    }

    /**
     * @return array
     */
    public function toAList()
    {
        return IteratorUtil::toAList($this->getIterator());
    }

    /**
     * @param null|int $depth
     * @return array
     */
    public function toAListRec($depth = null)
    {
        return IteratorUtil::toAListRec($this->getIterator(), $depth);
    }

    /**
     * @param string|callable $predicate ($value, $key)
     * @return bool
     */
    public function any($predicate)
    {
        $p = PredicateParser::parse($predicate);
        foreach ($this->getIterator() as $k => $v) {
            if ($p->predicate($v, $k) == true) {
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
        $p = PredicateParser::parse($predicate);
        foreach ($this->getIterator() as $k => $v) {
            if ($p->predicate($v, $k) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \Closure|string|null $predicate ($value, $key)
     * @return int
     */
    public function count($predicate = null)
    {
        if (is_null($predicate)) {
            return $this->foldLeft(0, function ($acc) { return $acc + 1; });
        } else {
            $p = PredicateParser::parse($predicate);
            return $this->foldLeft(0,
                function ($acc, $v, $k) use ($p) {
                    return ($p->predicate($v, $k)) ? ($acc + 1) : $acc;
                }
            );
        }
    }

    /**
     * @param \Closure|string|null $selector ($value, $key)
     * @return int
     */
    public function sum($selector = null)
    {
        $s = SelectorParser::parse($selector, ValueSelector::getInstance());
        return $this->foldLeft(0,
            function ($acc, $v, $k) use($s) {
                return $acc + $s->select($v, $k);
            }
        );
    }

    /**
     * @param \Closure|string|null $selector ($value, $key)
     * @return float
     */
    public function average($selector = null)
    {
        $s = SelectorParser::parse($selector, ValueSelector::getInstance());
        $sum   = 0;
        $count = 0;
        foreach ($this as $k => $v) {
            $count++;
            $sum += $s->select($v, $k);
        }
        return $sum / $count;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function first($default = null)
    {
        $it = $this->getIterator();
        $it->rewind();
        if ($it->valid()) {
            return $it->current();
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
        $it = $this->getIterator();
        $it->rewind();
        if ($it->valid()) {
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
        $eqComparer = EqualityComparer::getDefault();
        return $this->any(
            function ($v, $k) use ($value, $eqComparer) {
                return $eqComparer->equals($v, $value);
            }
        );
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function containsKey($key)
    {
        $eqComparer = EqualityComparer::getDefault();
        return $this->any(
            function ($v, $k) use ($key, $eqComparer) {
                return $eqComparer->equals($k, $key);
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
        foreach ($this->getIterator() as $k => $v) {
            if ($predicate($v, $k)) {
                return $v;
            }
        }
        return $default;
    }

    /**
     * @param mixed $accumulator
     * @param callable $operator (acc, v, k) -> acc
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
     * @param callable $operator (acc, v, k) -> acc
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
     * @param \Closure $operator ($accumulator, $value, $key)
     * @return mixed
     * @throws LengthException
     */
    public function reduceLeft($operator)
    {
        $it = $this->getIterator();
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
     * @param \Closure $operator ($accumulator, $value, $key)
     * @return mixed
     * @throws LengthException
     */
    public function reduceRight($operator)
    {
        $it = $this->reverse()->getIterator();
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
        return self::from(self::$gen->cycle(IteratorUtil::iterator($xs)));
    }

    /**
     * @param array|\Traversable $xs
     * @return Ginq
     */
    public static function from($xs)
    {
        if ($xs instanceof self) {
            return $xs;
        } else {
            return new self(IteratorUtil::iterator($xs));
        }
    }

    /**
     * @return Ginq
     */
    public function renum()
    {
        return self::from(self::$gen->renum($this->getIterator()));
    }

    /**
     * @param \Closure $fn
     * @return Ginq
     */
    public function each($fn)
    {
        return self::from(self::$gen->each($this->getIterator(), $fn));
    }

    /**
     * @param Closure|string|int|Selector|null $valueSelector
     * @param Closure|string|int|Selector|null $keySelector
     * @return Ginq
     */
    public function select($valueSelector = null, $keySelector = null)
    {
        return self::from(self::$gen->select(
            $this->getIterator(),
            SelectorParser::parse($valueSelector, ValueSelector::getInstance()),
            SelectorParser::parse($keySelector, KeySelector::getInstance())
        ));
    }

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function where($predicate)
    {
        return self::from(self::$gen->where(
            $this->getIterator(),
            PredicateParser::parse($predicate)
        ));
    }

    /**
     * @return Ginq
     */
    public function reverse()
    {
        return self::from(self::$gen->reverse($this->getIterator()));
    }

    /**
     * @param int $n
     * @return Ginq
     */
    public function take($n)
    {
        return self::from(self::$gen->take($this->getIterator(), $n));
    }

    /**
     * @param int $n
     * @return Ginq
     */
    public function drop($n)
    {
        return self::from(self::$gen->drop($this->getIterator(), $n));
    }

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function takeWhile($predicate)
    {
        return self::from(self::$gen->takeWhile(
            $this->getIterator(),
            PredicateParser::parse($predicate)
        ));
    }

    /**
     * @param string|callable $predicate
     * @return Ginq
     */
    public function dropWhile($predicate)
    {
        return self::from(self::$gen->dropWhile(
            $this->getIterator(), PredicateParser::parse($predicate)
        ));
    }

    /**
     * @param array|Traversable $rhs
     * @return Ginq
     */
    public function concat($rhs)
    {
        return self::from(self::$gen->concat(
            $this->getIterator(),
            self::from(IteratorUtil::iterator($rhs))
        ));
    }

    /**
     * @param Closure|string|int|Selector $manySelector
     * @param Closure|JoinSelector|int|null   $resultValueSelector
     * @param Closure|JoinSelector|int|null   $resultKeySelector
     * @return Ginq
     */
    public function selectMany($manySelector, $resultValueSelector = null, $resultKeySelector = null)
    {
        if (is_null($resultValueSelector) && is_null($resultKeySelector)) {
            return self::from(self::$gen->selectMany(
                $this->getIterator(),
                SelectorParser::parse($manySelector, ValueSelector::getInstance())));
        } else {
            return self::from(self::$gen->selectManyWithJoin(
                $this->getIterator(),
                SelectorParser::parse($manySelector, ValueSelector::getInstance()),
                JoinSelectorParser::parse($resultValueSelector, ValueJoinSelector::getInstance()),
                JoinSelectorParser::parse($resultKeySelector, KeyJoinSelector::getInstance())
            ));
        }
    }

    /**
     * @deprecated
     * @param Closure|string|int|Selector     $manySelector
     * @param Closure|JoinSelector|int|null   $resultValueSelector
     * @param Closure|JoinSelector|int|null   $resultKeySelector
     * @return Ginq
     */
    public function selectManyWith($manySelector, $resultValueSelector = null, $resultKeySelector = null)
    {
        return $this->selectMany($manySelector, $resultValueSelector, $resultKeySelector);
    }

    /**
     * @param array|Traversable $inner
     * @param \Closure|string|int|Selector   $outerCompareKeySelector
     * @param \Closure|string|int|Selector   $innerCompareKeySelector
     * @param \Closure|JoinSelector|int      $resultValueSelector
     * @param \Closure|JoinSelector|int|null $resultKeySelector
     * @return Ginq
     */
    public function join($inner,
            $outerCompareKeySelector, $innerCompareKeySelector,
            $resultValueSelector, $resultKeySelector = null)
    {
        return self::from(self::$gen->join(
            $this->getIterator(),
            IteratorUtil::iterator($inner),
            SelectorParser::parse($outerCompareKeySelector, ValueSelector::getInstance()),
            SelectorParser::parse($innerCompareKeySelector, ValueSelector::getInstance()),
            JoinSelectorParser::parse($resultValueSelector, ValueJoinSelector::getInstance()),
            JoinSelectorParser::parse($resultKeySelector,   KeyJoinSelector::getInstance()),
            EqualityComparerParser::parse(null, EqualityComparer::getDefault())
        ));
    }

    /**
     * @param array|Traversable    $rhs
     * @param Closure|JoinSelector|int      $resultValueSelector
     * @param Closure|JoinSelector|int|null $resultKeySelector
     * @return Ginq
     */
    public function zip($rhs, $resultValueSelector, $resultKeySelector = null)
    {
        return self::from(self::$gen->zip(
            $this->getIterator(),
            IteratorUtil::iterator($rhs),
            JoinSelectorParser::parse($resultValueSelector, ValueJoinSelector::getInstance()),
            JoinSelectorParser::parse($resultKeySelector,   KeyJoinSelector::getInstance())
        ));
    }

    /**
     * @param Closure|string|int|Selector      $compareKeySelector
     * @param Closure|string|int|Selector|null $elementSelector
     * @return Ginq
     */
    public function groupBy($compareKeySelector, $elementSelector = null)
    {
        return self::from(self::$gen->groupBy(
            $this->getIterator(),
            SelectorParser::parse($compareKeySelector, ValueSelector::getInstance()),
            SelectorParser::parse($elementSelector, ValueSelector::getInstance()),
            new DelegateSelector(function ($xs, $key) { return new GroupingGinq($xs, $key); }),
            EqualityComparerParser::parse(null, EqualityComparer::getDefault())
        ));
        ));
    }

    /**
     * @param Closure|string|int|Selector|null $compareKeySelector (v, k) -> comparable
     * @return OrderedGinq
     */
    public function orderBy($compareKeySelector = null)
    {
        $compareKeySelector = SelectorParser::parse($compareKeySelector, ValueSelector::getInstance());
        $comparer = ComparerParser::parse(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        return new OrderedGinq($this->getIterator(), $comparer);
    }

    /**
     * @param Closure|string|int|Selector|null $compareKeySelector (v, k) -> comparable
     * @return OrderedGinq
     */
    public function orderByDesc($compareKeySelector = null)
    {
        $compareKeySelector = SelectorParser::parse($compareKeySelector, ValueSelector::getInstance());
        $comparer = ComparerParser::parse(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        $comparer = new ReverseComparer($comparer);
        return new OrderedGinq($this->getIterator(), $comparer);
    }

    /**
     * @return Ginq
     */
    public function distinct()
    {
        return self::from(
            self::$gen->distinct($this->getIterator(),
            EqualityComparerParser::parse(null, EqualityComparer::getDefault())
        ));
    }

    /**
     * @return Ginq
     */
    public function memoize()
    {
        return self::from(self::$gen->memoize($this->getIterator()));
    }

    /*
    public function __callStatic($name, $args) {
        if (isset(self::$registeredStaticFunctions[$name])) {
            call_user_func_array(
                self::$registeredStaticFunctions[$name], $args
            );
        }
    }*/

    //private static $registeredStaticFunctions = array();

    public function __call($name, $args)
    {
        if (isset(self::$registeredFunctions[$name])) {
            call_user_func_array(
                self::$registeredFunctions[$name],
                array_merge(array($this), $args)
            );
        }
    }

    private static $registeredFunctions = array();


    public static function register($className)
    {
        $ref = new \ReflectionClass($className);

        $funcNames = Ginq::from($ref->getMethods(ReflectionMethod::IS_STATIC))
            ->where(function ($m) {
                /** @var $m \ReflectionMethod  */
                return $m->isPublic();
            })
            ->where(function ($m) {
                /** @var $m \ReflectionMethod  */
                /** @var $p \ReflectionParameter */
                $p = Ginq::from($m->getParameters())->first(false);
                if ($p === false) return false;

                $c = $p->getClass();

                return ($c->getName() === 'Ginq') or $c->isSubclassOf('Ginq');
            })
            ->select(function ($m) {
                /** @var $m \ReflectionMethod  */
                return $m->getName();
            });

        foreach ($funcNames as $func) {
            self::$registeredFunctions[$func] = array($className, $func);
        }
    }

    public static function listRegisteredFunctions()
    {
        return self::$registeredFunctions;
    }

    public static function _registerAutoloadFunction()
    {
        spl_autoload_register(function ($class) {
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

Ginq::useIterator();
