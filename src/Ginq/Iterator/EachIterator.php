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
namespace Ginq\Iterator;

use Ginq\Util\IteratorUtil;

class EachIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    protected $it;

    /**
     * @var \Closure
     */
    protected $fn;

    /**
     * @var bool
     */
    protected $fetched;

    /**
     * @var mixed
     */
    protected $v;

    /**
     * @var mixed
     */
    protected $k;

    /**
     * @param \Iterator $xs
     * @param \Closure $fn
     */
    public function __construct($xs, $fn)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->fn = $fn;
    }

    public function current()
    {
        $this->fetchOnce();
        $fn = $this->fn;
        $fn($this->v, $this->k);
        return $this->v;
    }

    public function next()
    {
        $this->it->next();
        $this->fetched = false;
    }

    public function key()
    {
        $this->fetchOnce();
        return $this->k;
    }

    public function valid()
    {
        return $this->it->valid();
    }

    public function rewind()
    {
        $this->fetched = false;
        $this->it->rewind();
    }

    private function fetchOnce()
    {
        if ($this->fetched) return;
        $this->v = $this->it->current();
        $this->k = $this->it->key();
        $this->fetched = true;
    }
}

