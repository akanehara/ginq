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

class Dictionary implements \IteratorAggregate
{
    /**
     * @var EqualityComparer
     */
    protected $eqComparer;

    /**
     * @var array
     */
    protected $keys;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param EqualityComparer $eqComparer
     */
    public function __construct($eqComparer = null)
    {
        if (is_null($eqComparer)) {
            $eqComparer = EqualityComparer::getDefault();
        }
        $this->eqComparer = $eqComparer;
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
        $hash = $this->eqComparer->hash($key);
        return array_key_exists($hash, $this->values);
    }

    /**
     * @param mixed      $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $hash = $this->eqComparer->hash($key);
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
        $hash = $this->eqComparer->hash($key);
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

