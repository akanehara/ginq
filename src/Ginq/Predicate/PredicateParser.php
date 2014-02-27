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

use Ginq\Util\FuncUtil;

class PredicateParser
{
    public static function parse($src)
    {
        if ($src instanceof \Closure) {
            return new DelegatePredicate($src);
        }
        if (is_array($src)) {
            return new DelegatePredicate(FuncUtil::fun($src));
        }
        $type = gettype($src);
        throw new \InvalidArgumentException(
            "'predicate' callable expected, got $type");
    }
}
