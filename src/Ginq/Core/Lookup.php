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
     * @var Dictionary
     */
    private $dict;

    protected function __construct($equalityComparer)
    {
        $this->dict = new Dictionary($equalityComparer);
    }

    /**
     * @param array|\Iterator|\IteratorAggregate|\Traversable $xs
     * @param Selector $keySelector
     * @return Lookup
     */
    public static function from($xs, $keySelector, $equalityComparer = null)
    {
        $lookup = new self($equalityComparer);
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
        return $this->dict->getIterator();
    }

    /**
     * @param mixed $key
     * @return array
     */
    public function get($key)
    {
        /**
         * @var \ArrayObject $xs
         */
        $xs = $this->dict->get($key, array());
        return $xs;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function put($key, $value)
    {
        /**
         * @var \ArrayObject $xs
         */
        if (!$this->dict->contains($key)) {
            $this->dict->put($key, new \ArrayObject());
        }
        $xs = $this->dict->get($key);
        $xs->append($value);
    }
}

