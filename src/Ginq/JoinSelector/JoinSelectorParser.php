<?php
/**
 * Ginq: Generator INtegrated Query
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
