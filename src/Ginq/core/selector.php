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
namespace Ginq\core\selector;

/**
 * @param int|null
 * @throws InvalidArgumentException
 * @return callable
 * @package Ginq
 */
function seq($start = 0)
{
    $i0 = $start;
    $i = $i0;
    return function() use (&$i) {
        return $i++;
    };
}

/**
 * @param mixed
 * @throws InvalidArgumentException
 * @return Iterator
 * @package Ginq
 */
function value($v, $k)
{
    return $v;
}

/**
 * @param mixed
 * @throws InvalidArgumentException
 * @return Iterator
 * @package Ginq
 */
function key($v, $k)
{
    return $k;
}

