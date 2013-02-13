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
use Ginq\Core\Selector;
use Ginq\Core\JoinSelector;

/**
 * SelectManyWithJoinIterator
 * @package Ginq
 */
class SelectManyWithJoinIterator implements \Iterator
{
    /**
     * @var Selector
     */
    private $manySelector;

    /**
     * @var JoinSelector
     */
    private $valueJoinSelector;

    /**
     * @var JoinSelector
     */
    private $keyJoinSelector;

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
     * @param $xs
     * @param Selector $manySelector
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     */
    public function __construct($xs, $manySelector, $valueJoinSelector, $keyJoinSelector)
    {
        $this->outer = IteratorUtil::iterator($xs);
        $this->manySelector = $manySelector;
        $this->valueJoinSelector = $valueJoinSelector;
        $this->keyJoinSelector = $keyJoinSelector;
    }

    public function current()
    {
        return $this->valueJoinSelector->joinSelect(
            $this->outer->current(),
            $this->inner->current(),
            $this->outer->key(),
            $this->inner->key()
        );
    }

    public function key() 
    {
        return $this->keyJoinSelector->joinSelect(
            $this->outer->current(),
            $this->inner->current(),
            $this->outer->key(),
            $this->inner->key()
        );
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
