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

require_once dirname(__FILE__) . "/iter.php";

/**
 * Lookup
 * @package Ginq
 */
class Lookup implements IteratorAggregate
{
    private $table = null;

    protected function __construct()
    {
        $this->table = array();
    }
    
    public static function from($xs, $keySelector)
    {
        $lookup = new Lookup();
        foreach ($xs as $x) {
            $lookup->put($keySelector($x), $x);
        }
        return $lookup;
    }

    public function getIterator()
    {
        return iter($this->table);
    }

    public function get($key)
    {
        @$v = $this->table[$key];
        if (is_array($v)) {
            return $v;
        }
        return array();
    }

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

