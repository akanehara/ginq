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
use Ginq\Core\JoinSelector;
use Ginq\Core\Comparer;
use Ginq\Selector\SelectorParser;
use Ginq\JoinSelector\JoinSelectorParser;
use Ginq\Predicate\PredicateParser;
use Ginq\Comparer\CompoundComparer;
use Ginq\Util\IteratorUtil;
use Ginq\Comparer\ReverseComparer;
use Ginq\Comparer\DefaultComparer;
use Ginq\Comparer\ProjectionComparer;
use Ginq\Comparer\ComparerParser;

class OrderedGinq extends Ginq
{
    /**
     * @var Comparer
     */
    protected $comparer;

    /**
     * @param \Iterator $it
     * @param \Ginq\Core\Comparer $comparer
     */
    protected function __construct($it, $comparer)
    {
        $this->comparer = $comparer;
        parent::__construct($it);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return self::$gen->orderBy($this->it, $this->comparer);
    }

    /**
     * @param array|Iterator|IteratorAggregate|Traversable $xs
     * @param Comparer $comparer
     * @return OrderedGinq
     */
    public static function create($xs, $comparer)
    {
        return new OrderedGinq($xs, $comparer);
    }

    /**
     * @param Closure|string|int|Selector|null $orderingKeySelector
     * @param Closure|Comparer|null $comparer
     * @return OrderedGinq
     */
    public function thenBy($orderingKeySelector = null, $comparer = null)
    {
        if (is_null($orderingKeySelector)) {
            $orderingKeySelector = Ginq::VALUE_OF;
        }
        $orderingKeySelector = SelectorParser::parse($orderingKeySelector);
        $comparer = ComparerParser::parse($comparer);
        $comparer = new ProjectionComparer($orderingKeySelector, $comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return OrderedGinq::create($this->getIterator(), $comparer);
    }

    /**
     * @param Closure|string|int|Selector|null $orderingKeySelector
     * @param Closure|Comparer|null $comparer
     * @return OrderedGinq
     */
    public function thenByDesc($orderingKeySelector = null, $comparer = null)
    {
        if (is_null($orderingKeySelector)) {
            $orderingKeySelector = Ginq::VALUE_OF;
        }
        $orderingKeySelector = SelectorParser::parse($orderingKeySelector);
        $comparer = ComparerParser::parse($comparer);
        $comparer = new ProjectionComparer($orderingKeySelector, $comparer);
        $comparer = new ReverseComparer($comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return OrderedGinq::create($this->getIterator(), $comparer);
    }
}

