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

namespace Ginq\Util;

class IteratorUtil
{
    /**
     * @param array|\Traversable $xs
     * @return \Iterator
     * @throws \InvalidArgumentException
     */
    static public function iterator($xs) {
        if ($xs instanceof \Iterator) {
            return $xs;
        } else if ($xs instanceof \IteratorAggregate) {
            return $xs->getIterator();
        } else if (is_array($xs)) {
            return new \ArrayIterator($xs);
        } else {
            $t = gettype($xs);
            throw new \InvalidArgumentException("'$t' object is not iterable");
        }
    }

    /**
     * @param \Traversable $it
     * @return array
     */
    public static function toList($it)
    {
        $acc = array();
        foreach ($it as $v) {
            $acc[] = $v;
        }
        return $acc;
    }


    /**
     * @param \Traversable $it
     * @param null|int $depth
     * @return array
     */
    public static function toListRec($it, $depth = null)
    {
        if ($depth === 1) {
            return self::toList($it);
        } else {
            $acc = array();
            foreach ($it as $v) {
                if ($v instanceof \Traversable) {
                    $acc[] = self::toListRec($v, $depth - 1);
                } else {
                    $acc[] = $v;
                }
            }
            return $acc;
        }
    }

    /**
     * @param \Traversable $it
     * @return array
     */
    public static function toAList($it)
    {
        $acc = array();
        foreach ($it as $k => $v) {
            $acc[] = array($k, $v);
        }
        return $acc;
    }

    /**
     * @param \Traversable $it
     * @param null|int $depth
     * @return array
     */
    public static function toAListRec($it, $depth = null)
    {
        if ($depth === 1) {
            return self::toAList($it);
        } else {
            $acc = array();
            foreach ($it as $k => $v) {
                if ($v instanceof \Traversable) {
                    $acc[] = array($k, self::toAListRec($v, $depth - 1));
                } else {
                    $acc[] = array($k, $v);
                }
            }
            return $acc;
        }
    }

    /**
     * @param \Traversable $it
     * @return array
     */
    public static function toArray($it)
    {
        $acc = array();
        foreach ($it as $k => $v) {
            $acc[$k] = $v;
        }
        return $acc;
    }

    /**
     * @param \Traversable $it
     * @param \Closure     $combiner (existVal, v, k) -> v
     * @return array
     */
    public static function toArrayWithCombine($it, $combiner)
    {
        $acc = array();
        foreach ($it as $k => $v) {
            if (!array_key_exists($k, $acc)) {
                $acc[$k] = $v;
            } else {
                $acc[$k] = $combiner($acc[$k], $v, $k);
            }
        }
        return $acc;
    }

    /**
     * @param \Traversable $it
     * @param int|null     $depth
     * @return array
     */
    public static function toArrayRec($it, $depth)
    {
        if ($depth === 1) {
            return self::toArray($it);
        } else {
            $acc = array();
            foreach ($it as $k => $v) {
                if ($v instanceof \Traversable) {
                    $acc[$k] = self::toArrayRec($v, $depth - 1);
                } else {
                    $acc[$k] = $v;
                }
            }
            return $acc;
        }
    }

    /**
     * @param \Traversable $it
     * @param int|null     $depth
     * @param \Closure     $combiner (existV, v, k) -> v
     * @return array
     */
    public static function toArrayRecWithCombine($it, $depth, $combiner)
    {
        $acc = array();
        foreach ($it as $k => $v) {
            if (!array_key_exists($k, $acc)) {
                $acc[$k] = $v;
            } else {
                $acc[$k] = $combiner($acc[$k], $v, $k);
            }
        }
        if ($depth !== 1) {
            foreach ($acc as $k => $v) {
                if ($v instanceof \Traversable) {
                    $acc[$k] = self::toArrayRecWithCombine($v, $depth - 1, $combiner);
                }
            }
        }
        return $acc;
    }

}
