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

class DelegateComparer extends Comparer
{
    /**
     * @var \Closure
     */
    private $fn;

    /**
     * @param \Closure $fn
     */
    public function __construct($fn)
    {
        $this->fn = $fn;
    }

    /**
     * @param mixed      $v0 - left value
     * @param mixed      $v1 - right value
     * @param mixed|null $k0 - left key
     * @param mixed|null $k1 - right key
     * @return int
     */
    public function compare($v0, $v1, $k0 = null, $k1 = null)
    {
        $fn = $this->fn;
        return $fn($v0, $v1, $k0, $k1);
    }
}

