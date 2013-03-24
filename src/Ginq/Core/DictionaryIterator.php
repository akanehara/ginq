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

class DictionaryIterator implements \Iterator
{
    /**
     * @var int
     */
    protected $i;

    /**
     * @var Dictionary
     */
    protected $dict;

    /**
     * @var \Iterator
     */
    protected $it;

    /**
     * @param Dictionary $dict
     */
    public function __construct($dict)
    {
        $this->dict = $dict;
        $this->it = new \ArrayIterator($this->dict->keys());
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it->rewind();
    }

    public function valid()
    {
        return $this->it->valid();
    }

    public function key()
    {
        return $this->i;
    }

    public function current()
    {
        $k = $this->it->current();
        $v = $this->dict->get($k);
        return new KeyValuePair($k, $v);
    }

    public function next()
    {
        $this->i++;
        $this->it->next();
    }
}