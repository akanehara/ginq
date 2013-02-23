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

namespace Ginq\Iterator;

use Ginq\Util\IteratorUtil;

class MemoizeIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var int
     */
    private $i;

    /**
     * @var array
     */
    private $cache;

    /**
     * @var int
     */
    private $cacheSize;


    /**
     * @param array|\Traversable $xs
     */
    public function __construct($xs)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->rewinded = false;
        $this->cache = array();
        $this->cacheSize = 0;
    }

    public function current()
    {
        return $this->cache[$this->i][1];
    }

    public function key()
    {
        return $this->cache[$this->i][0];
    }

    public function next()
    {
        $this->i++;
        if ($this->cacheSize == $this->i) {
            $this->it->next();
            $this->memo();
        }
    }

    public function valid()
    {
        return ($this->i < $this->cacheSize);
    }

    public function rewind()
    {
        $this->i = 0;
        if (!$this->rewinded) {
            $this->it->rewind();
            $this->memo();
            $this->rewinded = true;
        }
    }

    private function memo()
    {
        if (!$this->it->valid()) {
            return;
        }
        array_push(
            $this->cache,
            array($this->it->key(), $this->it->current())
        );
        $this->cacheSize++;
    }
}
