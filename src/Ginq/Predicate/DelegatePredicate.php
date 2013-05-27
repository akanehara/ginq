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

namespace Ginq\Predicate;

use Ginq\Core\Predicate;

class DelegatePredicate implements Predicate
{
    /**
     * @var \Closure
     */
    private $func;

    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * @param mixed $v
     * @param mixed $k
     * @return bool
     */
    public function predicate($v, $k)
    {
        $f = $this->func;
        return $f($v, $k);
    }
}

