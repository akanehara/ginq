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

use Ginq\Core\Selector;
use Ginq\Util\IteratorUtil;

/**
 * SelectManyIterator
 * @package Ginq
 */
class SelectManyIterator implements \Iterator
{

    /**
     * @var \Ginq\Core\Selector
     */
    private $manySelector;

    /**
     * @var \Iterator
     */
    private $outer;

    /**
     * @var \Iterator
     */
    private $inner;

    /**
     * @var int
     */
    private $i;

    /**
     * @param \Iterator $xs
     * @param Selector $manySelector
     */
    public function __construct($xs, $manySelector)
    {
        $this->outer = IteratorUtil::iterator($xs);
        $this->manySelector = $manySelector;
    }

    public function current()
    {
        return $this->inner->current();
    }

    public function key() 
    {
        return $this->inner->key();
    }

    public function next()
    {
        $this->i++;
        $this->inner->next();
        if (!$this->inner->valid()) {
            $this->outer->next();
            $this->nextInner();
        }
    }

    public function rewind()
    {
        $this->i = 0;
        $this->outer->rewind();
        $this->nextInner();
    }

    protected function nextInner()
    {
        if ($this->outer->valid()) {
            $this->inner = IteratorUtil::iterator(
                $this->manySelector->select(
                    $this->outer->current(),
                    $this->outer->key()
                )
            );
        }
    }

    public function valid()
    {
        return $this->inner->valid();
    }
}
