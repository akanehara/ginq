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

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


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

    /**
     * @param callable $f
     * @return callable
     */
    static public function partial($f)
    {
        $args = array_slice(func_get_args(), 1);
        return function() use ($f, $args) {
            array_merge($args, func_get_args());
            return call_user_func_array($f, $args);
        };
    }

    /**
     * g(f(x))
     * @param callable $g
     * @param callable $f
     * @return callable
     */
    static public function compose($g, $f)
    {
        return function($x) use ($g, $f) { return $g($f($x)); };
    }

    /**
     * @param mixed|\Closure $x
     * @return mixed
     */
    static public function force($x)
    {
        return ($x instanceof \Closure) ? $x() : $x;
    }

}

