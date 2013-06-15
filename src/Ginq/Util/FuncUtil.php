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

namespace Ginq\Util;

class FuncUtil
{
    /**
     * @param  mixed|\Closure $x
     * @return mixed
     */
    static public function applyOrItself($x) {
        if ($x instanceof \Closure) {
            return call_user_func_array($x, array_slice(func_get_args(), 1));
        } else {
            return $x;
        }
    }
}

