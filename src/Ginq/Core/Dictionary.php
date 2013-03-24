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

use \Ginq\Iterator\RenumIterator;

class Dictionary implements \IteratorAggregate
{
    /**
     * @var EqualityComparer
     */
    protected $comparer;

    /**
     * @var array
     */
    protected $keys;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param EqualityComparer $equalityComparer
     */
    public function __construct($equalityComparer = null)
    {
        if (is_null($equalityComparer)) {
            $equalityComparer = EqualityComparer::getDefault();
        }
        $this->comparer = $equalityComparer;
        $this->keys = array();
        $this->values = array();
    }

    public function keys()
    {
        return $this->keys;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function contains($key)
    {
        $hash = $this->comparer->hash($key);
        return array_key_exists($hash, $this->values);
    }

    /**
     * @param mixed $key
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $hash = $this->comparer->hash($key);
        if (array_key_exists($hash, $this->values)) {
            return $this->values[$hash];
        } else {
            return $default;
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     */
    public function put($key, $value)
    {
        $hash = $this->comparer->hash($key);
        if (!array_key_exists($hash, $this->values)) {
            $this->keys[] = $key;
        }
        $this->values[$hash] = $value;
    }


    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new DictionaryIterator($this);
    }
}

