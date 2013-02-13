<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/14
 * Time: 0:53
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Predicate;

use Ginq\Core\Predicate;

class PredicateParser
{
    public static function parse($src)
    {
        if (is_string($src)) {
            return new \Ginq\Predicate\PropertyPredicate($src);
        }

        if ($src instanceof \Closure) {
            return new \Ginq\Predicate\DelegatePredicate($src);
        }

        $type = gettype($src);
        throw new \InvalidArgumentException(
            "'predicate' callable expected, got $type");
    }
}
