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
use Ginq\Lambda\Lambda;
use Ginq\Util\FuncUtil;

class JoinSelectorResolver
{
    /**
     * @param \Closure|JoinSelector|int $src
     * @param $default
     * @throws \InvalidArgumentException
     * @return JoinSelector
     */
    public static function resolve($src, $default)
    {
        if (is_null($src)) {
            return $default;
        }
        if (is_callable($src)) {
            return new DelegateJoinSelector($src);
        }
        if (is_array($src)) {
            return new DelegateJoinSelector(Lambda::fun($src));
        }
        if ($src instanceof JoinSelector) {
            return $src;
        }
        $type = gettype($src);
        throw new \InvalidArgumentException(
            "Invalid selector, got $type");
    }
}
