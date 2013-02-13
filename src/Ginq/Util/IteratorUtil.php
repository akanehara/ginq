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
namespace Ginq\Util;

class IteratorUtil
{
    /**
     * @param $xs
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
}
