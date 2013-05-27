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

namespace Ginq\JoinSelector;

use Ginq\Core\JoinSelector;

class DelegateJoinSelector implements JoinSelector
{
    /**
     * @var callable
     */
    private $func;

    /**
     * @param \Closure $func
     */
    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * @param mixed $v0
     * @param mixed $v1
     * @param mixed $k0
     * @param mixed $k1
     * @return mixed
     */
    public function select($v0, $v1, $k0, $k1)
    {
        $f = $this->func;
        return $f($v0, $v1, $k0, $k1);
    }
}
