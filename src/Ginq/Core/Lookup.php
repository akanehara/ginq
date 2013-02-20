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

use Ginq\Core\Selector;
use Ginq\Util\IteratorUtil;

/**
 * Lookup
 *
 * @package Ginq
 */
class Lookup implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $table;

    protected function __construct()
    {
        $this->table = array();
    }

    /**
     * @param array|\Iterator|\IteratorAggregate|\Traversable $xs
     * @param Selector $keySelector
     * @return Lookup
     */
    public static function from($xs, $keySelector)
    {
        $lookup = new self();
        foreach ($xs as $k => $v) {
            $lookup->put($keySelector->select($v, $k), $v);
        }
        return $lookup;
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return IteratorUtil::iterator($this->table);
    }

    /**
     * @param mixed $key
     * @return array
     */
    public function get($key)
    {
        @$v = $this->table[$key];
        if (is_array($v)) {
            return $v;
        }
        return array();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function put($key, $value)
    {
        @$v = &$this->table[$key];
        if (is_array($v)) {
            array_push($v, $value);
        } else {
            $this->table[$key] = array($value);
        }
    }
}

