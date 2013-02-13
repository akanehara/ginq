<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\JoinSelector;

use Ginq\Core\JoinSelector;
use Ginq\Selector\SelectorParser;
use Ginq\Selector\CountSelector;

class JoinSelectorParser
{
    /**
     * @src \Closure|JoinSelector
     * @return JoinSelector
     */
    public static function parse($src)
    {
        if ($src instanceof \Closure) {
            return new DelegateJoinSelector($src);
        }

        if (is_int($src)) {
            switch ($src)
            {
                case SelectorParser::COUNTER:
                    return new CountSelector(0);
            }
        }

        if ($src instanceof JoinSelector) {
            return $src;
        }

        $type = gettype($src);
        throw new \InvalidArgumentException(
            "'join selector' Closure expected, got $type");
    }
}
