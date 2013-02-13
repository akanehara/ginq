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

use \Ginq\Core\Selector;
use \Ginq\Core\JoinSelector;
use \Ginq\Util\IteratorUtil;

/**
 * ZipIterator
 * @package Ginq
 */
class ZipIterator implements \Iterator
{
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
    private $it0;

    /**
     * @var \Iterator
     */
    private $it1;

    /**
     * @var int
     */
    private $i;

    /**
     * @param \Iterator $xs
     * @param \Iterator $ys
     * @param JoinSelector $valueJoinSelector
     * @param JoinSelector $keyJoinSelector
     */
    public function __construct($xs, $ys, $valueJoinSelector, $keyJoinSelector)
    {
        $this->valueJoinSelector = $valueJoinSelector;
        $this->keyJoinSelector = $keyJoinSelector;
        $this->it0 = IteratorUtil::iterator($xs);
        $this->it1 = IteratorUtil::iterator($ys);
    }

    public function current()
    {
        return $this->valueJoinSelector->joinSelect(
            $this->it0->current(),
            $this->it1->current(),
            $this->it0->key(),
            $this->it1->key()
        );
    }

    public function key() 
    {
        return $this->keyJoinSelector->joinSelect(
            $this->it0->current(),
            $this->it1->current(),
            $this->it0->key(),
            $this->it1->key()
        );
    }

    public function next()
    {
        $this->i++;
        $this->it0->next();
        $this->it1->next();
    }

    public function rewind()
    {
        $this->i = 0;
        $this->it0->rewind();
        $this->it1->rewind();
    }

    public function valid()
    {
        return $this->it0->valid() && $this->it1->valid();
    }
}
