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

namespace Ginq;

use Ginq\Core\Selector;
use Ginq\Core\Comparer;

use Ginq\Selector\ValueSelector;
use Ginq\Selector\SelectorResolver;
use Ginq\Comparer\CompoundComparer;

use Ginq\Comparer\ReverseComparer;
use Ginq\Comparer\ProjectionComparer;
use Ginq\Comparer\ComparerResolver;

class OrderingGinq extends Ginq
{
    /**
     * @var Comparer
     */
    protected $comparer;

    /**
     * @param \Iterator $it
     * @param \Ginq\Core\Comparer $comparer
     */
    public function __construct($it, $comparer)
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
     * @param \Closure|string|int|Selector|null $compareKeySelector
     * @return OrderingGinq
     */
    public function thenBy($compareKeySelector = null)
    {
        $compareKeySelector = SelectorResolver::resolve($compareKeySelector,ValueSelector::getInstance());
        $comparer = ComparerResolver::resolve(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderingGinq($this->it, $comparer);
    }

    /**
     * @param \Closure|string|int|Selector|null $compareKeySelector
     * @return OrderingGinq
     */
    public function thenByDesc($compareKeySelector = null)
    {
        $compareKeySelector = SelectorResolver::resolve($compareKeySelector, ValueSelector::getInstance());
        $comparer = ComparerResolver::resolve(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        $comparer = new ReverseComparer($comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderingGinq($this->it, $comparer);
    }

    /**
     * @param callable|array $comparer (v1, v2, k1?, k2?) -> int
     * @return OrderingGinq
     */
    public function thenWith($comparer)
    {
        $comparer = ComparerResolver::resolve($comparer, Comparer::getDefault());
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderingGinq($this->it, $comparer);
    }

    /**
     * @param callable|array $comparer (v1, v2, k1?, k2?) -> int
     * @return OrderingGinq
     */
    public function thenWithDesc($comparer)
    {
        $comparer = ComparerResolver::resolve($comparer, Comparer::getDefault());
        $comparer = new ReverseComparer($comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderingGinq($this->it, $comparer);
    }
}

