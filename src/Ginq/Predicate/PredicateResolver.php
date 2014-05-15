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
use Ginq\Lambda\Lambda;

class PredicateResolver
{
    /**
     * @param callable|string|array|Predicate $src
     * @throws \InvalidArgumentException
     * @return Predicate
     */
    public static function resolve($src)
    {
        if (is_callable($src)) {
            return new DelegatePredicate($src);
        }
        if (is_string($src)) {
            return new PropertyPredicate($src);
        }
        if (is_array($src)) {
            return new DelegatePredicate(Lambda::fun($src));
        }
        if ($src instanceof Predicate) {
            return $src;
        }
        $type = gettype($src);
        throw new \InvalidArgumentException(
            "Invalid predicate, got $type");
    }
}
