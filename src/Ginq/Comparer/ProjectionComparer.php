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

namespace Ginq\Comparer;

use Ginq\Core\Comparer;
use Ginq\Core\Selector;

class ProjectionComparer extends Comparer
{
    /**
     * @var Selector
     */
    private $sortKeySelector;

    /**
     * @var Comparer
     */
    private $comparer;

    public function __construct($sortKeySelector, $comparer)
    {
        $this->sortKeySelector = $sortKeySelector;
        $this->comparer = $comparer;
    }

    /**
     * @param mixed $v0 - left value (sort key)
     * @param mixed $v1 - right value (sort key)
     * @param mixed $k0 - left key
     * @param mixed $k1 - right key
     * @return int
     */
    public function compare($v0, $v1, $k0, $k1)
    {
        return $this->comparer->compare(
            $this->sortKeySelector->select($v0, $k0),
            $this->sortKeySelector->select($v1, $k1),
            $k0, $k1
        );
    }
}
