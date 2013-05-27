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

namespace Ginq\EqualityComparer;

use Ginq\Core\EqualityComparer;

class DelegateEqualityComparer extends EqualityComparer
{
    /* @var \Closure */
    protected $equalsFn;

    /* @var \Closure */
    protected $hashFn;

    /**
     * @param \Closure $equalsFn
     * @param \Closure $hashFn
     */
    public function __construct($equalsFn, $hashFn)
    {
        $this->equalsFn = $equalsFn;
        $this->hashFn   = $hashFn;
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
        $f = $this->equalsFn;
        return $f($v0, $v1);
    }

    public function hash($v)
    {
        $f = $this->hashFn;
        return $f($v);
    }
}
