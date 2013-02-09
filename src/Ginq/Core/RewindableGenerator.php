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
namespace Ginq\core;

class RewindableGenerator implements \Iterator
{
    protected $gen0;
    protected $gen;
 
    public function __construct(\Generator $generator)
    {
        $this->gen0 = $generator;
        $this->gen = null;
    }
 
    public function rewind()
    {
        $this->gen = clone $this->gen0;
        $this->gen->rewind();
    }
 
    public function valid()
    {
        if (!$this->gen) { $this->gen = clone $this->gen0; }
        return $this->gen->valid();
    }
 
    public function current()
    {
        if (!$this->gen) { $this->gen = clone $this->gen0; }
        return $this->gen->current();
    }
 
    public function key()
    {
        if (!$this->gen) { $this->gen = clone $this->gen0; }
        return $this->gen->key();
    }
 
    public function next()
    {
        if (!$this->gen) { $this->gen = clone $this->gen0; }
        $this->gen->next();
    }
 
    public function send($value)
    {
        if (!$this->gen) { $this->gen = clone $this->gen0; }
        return $this->gen->send($value);
    }
 
    public function close()
    {
        $this->gen0->close();
        if ($this->gen) {
            $this->gen->close();
        }
    }
}

