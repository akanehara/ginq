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
 * SelectIterator
 * @package Ginq
 */
class SelectIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $it;

    /**
     * @var Selector
     */
    private $valueSelector;

    /**
     * @var Selector
     */
    private $keySelector;

    /**
     * @var int
     */
    private $i;

    /**
     * @param $xs
     * @param Selector $valueSelector
     * @param Selector $keySelector
     */
    public function __construct($xs, $valueSelector, $keySelector)
    {
        $this->it = IteratorUtil::iterator($xs);
        $this->valueSelector = $valueSelector;
        $this->keySelector = $keySelector;
    }

    public function current()
    {
        return $this->valueSelector->select(
            $this->it->current(), $this->it->key()
        );
    }

    public function key() 
    {
        return $this->keySelector->select(
            $this->it->current(), $this->it->key()
        );
    }

    public function next()
    {
        $this->i++;
        $this->it->next();
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
}
