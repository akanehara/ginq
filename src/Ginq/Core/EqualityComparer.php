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

namespace Ginq\Core;

class EqualityComparer
{
    /**
     * @var EqualityComparer
     */
    static private $inst;

    /**
     * @return EqualityComparer
     */
    static final public function getDefault()
    {
        if (is_null(self::$inst)) {
            self::$inst = new EqualityComparer();
        }
        return self::$inst;
    }

    public function equals($x, $y)
    {
        if (is_object($x) && is_object($y)) {
            return $x == $y;
        }
        /* if (is_array($x) && is_array($y)) {
            return $x === $y;
        } */
        return $x === $y;
    }

    public function hash($v)
    {
        return sha1(serialize($v));
    }
}

