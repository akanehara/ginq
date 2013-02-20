<?php
namespace Ginq\Comparer;

use Ginq\Core\Comparer;

class ComparerParser
{
    /**
     * @param \Closure|Comparer $src
     */
    static public function parse($src)
    {
        if (is_null($src)) {
            return new DefaultComparer();
        }
        if ($src instanceof \Closure) {
            return new DelegateComparer($src);
        }
        if ($src instanceof Comparer) {
            return $src;
        }
        $type = gettype($src);
        throw new \InvalidArgumentException(
            "'comparer' Closure expected, got $type");
    }
}

