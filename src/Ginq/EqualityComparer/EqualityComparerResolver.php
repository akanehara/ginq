<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 2013/05/27
 * Time: 16:21
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\EqualityComparer;

use Ginq\Comparer\DelegateComparer;
use Ginq\Core\EqualityComparer;

class EqualityComparerResolver
{
    /**
     * @param callable|EqualityComparer $src
     * @param EqualityComparer $default
     * @throws \InvalidArgumentException
     * @return \Ginq\Core\EqualityComparer
     */
    static public function resolve($src, $default)
    {
        if (is_null($src)) {
            return $default;
        }
        if ($src instanceof EqualityComparer) {
            return $src;
        }
        $type = gettype($src);
        throw new \InvalidArgumentException(
            "Invalid equality comparer, got $type");
    }
}