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

namespace Ginq\Selector;

use Ginq\Core\Selector;

class SelectorParser
{
    const COUNTER  = 1;
    const VALUE_OF = 2;
    const KEY_OF   = 3;

    /**
     * @param \Closure|string|int|Selector $src
     * @return Selector
     * @throws \InvalidArgumentException
     */
    public static function parse($src)
    {
        if (is_string($src)) {
            return new PropertySelector($src);
        }

        if (is_callable($src)) {
            return new ProjectionSelector($src);
        }

        if (is_int($src)) {
            switch ($src)
            {
                case self::COUNTER:
                    return new CountSelector(0);
                case self::VALUE_OF:
                    return IdentityValueSelector::getInstance();
                case self::KEY_OF:
                    return IdentityKeySelector::getInstance();
            }
        }

        if ($src instanceof Selector) {
            return $src;
        }

        $type = gettype($src);
        throw new \InvalidArgumentException(
            "'selector' Closure or string or expected, got $type");
    }
}

