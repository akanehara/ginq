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

    /**
     * @param mixed      $v0 - left value
     * @param mixed      $v1 - right value
     * @param mixed|null $k0 - left key
     * @param mixed|null $k1 - right key
     * @return bool
     */
    public function equals($v0, $v1, $k0 = null, $k1 = null)
    {
        if (is_object($v0) && is_object($v1)) {
            return $v0 == $v1;
        }
        /* if (is_array($x) && is_array($y)) {
            return $x === $y;
        } */
        return $v0 === $v1;
    }

    public function hash($v)
    {
        return sha1(serialize($v));
    }
}

