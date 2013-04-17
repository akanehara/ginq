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
use Ginq\Core\JoinSelector;
use Ginq\Core\Comparer;
use Ginq\Selector\SelectorParser;
use Ginq\JoinSelector\JoinSelectorParser;
use Ginq\Predicate\PredicateParser;
use Ginq\Comparer\CompoundComparer;
use Ginq\Util\IteratorUtil;
use Ginq\Comparer\ReverseComparer;
use Ginq\Comparer\ProjectionComparer;
use Ginq\Comparer\ComparerParser;

class GroupingGinq extends \Ginq
{
    /**
     * @var mixed
     */
    protected $key;

    /**
     * @param \Iterator $it
     * @param \Ginq\Core\Comparer $comparer
     */
    public function __construct($it, $key)
    {
        $this->key = $key;
        parent::__construct($it);
    }

    public function key()
    {
        return $this->key;
    }
}
