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

namespace Ginq\Core;

interface JoinSelector
{
    /**
     * @param mixed $v0 value0
     * @param mixed $v1 value1
     * @param mixed $k0 key0
     * @param mixed $k1 key1
     * @return mixed selected
     */
    public function select($v0, $v1, $k0, $k1);
}

