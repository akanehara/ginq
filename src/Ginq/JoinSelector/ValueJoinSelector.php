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

class ValueJoinSelector implements JoinSelector
{
    /**
     * @var ValueJoinSelector
     */
    static private $inst;

    /**
     * @return ValueJoinSelector
     */
    static public function getInstance() {
        if (is_null(self::$inst)) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * @param mixed $v0
     * @param mixed $v1
     * @param mixed $k0
     * @param mixed $k1
     * @return mixed
     */
    public function select($v0, $v1, $k0, $k1)
    {
        return $v1;
    }
}

