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
use Ginq\Selector\KeySelector;
use Ginq\Selector\ValueSelector;
use Ginq\Selector\SelectorParser;
use Ginq\Comparer\CompoundComparer;
use Ginq\Util\IteratorUtil;
use Ginq\Comparer\ReverseComparer;
use Ginq\Comparer\ProjectionComparer;
use Ginq\Comparer\ComparerParser;

class OrderedContext extends GinqContext
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
     * @return OrderedContext
     */
    public function thenBy($compareKeySelector = null)
    {
        $compareKeySelector = SelectorParser::parse($compareKeySelector,ValueSelector::getInstance());
        $comparer = ComparerParser::parse(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderedContext($this->it, $comparer);
    }

    /**
     * @param \Closure|string|int|Selector|null $compareKeySelector
     * @return OrderedContext
     */
    public function thenByDesc($compareKeySelector = null)
    {
        $compareKeySelector = SelectorParser::parse($compareKeySelector, ValueSelector::getInstance());
        $comparer = ComparerParser::parse(null, Comparer::getDefault());
        $comparer = new ProjectionComparer($compareKeySelector, $comparer);
        $comparer = new ReverseComparer($comparer);
        $comparer = new CompoundComparer($this->comparer, $comparer);
        return new OrderedContext($this->it, $comparer);
    }
}

