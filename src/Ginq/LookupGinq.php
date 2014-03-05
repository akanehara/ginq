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

namespace Ginq;

use Ginq\Core\Dictionary;
use Ginq\Core\EqualityComparer;

use Ginq\Selector\ValueSelector;
use Ginq\Util\IteratorUtil;

/**
 * Lookup
 *
 * @package Ginq
 */
class LookupGinq extends Ginq
{
    /**
     * @var Dictionary
     */
    private $dict;

    /**
     * @var array
     */
    private $keys;

    /**
     * @param EqualityComparer $eqComparer
     */
    public function __construct($eqComparer)
    {
        $this->dict = new Dictionary($eqComparer);
        $this->keys = array();
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        $dict = $this->dict;
        $it = Ginq::from($dict->keys())
            ->select(
                function($key) use (&$dict) {
                    return new GroupingGinq(IteratorUtil::iterator($dict->get($key)), $key);
                },
                ValueSelector::getInstance()
            )->getIterator();
        return $it;
    }

    /**
     * @param mixed $key
     * @return GroupingGinq
     */
    public function get($key)
    {
        /**
         * @var \ArrayObject $xs
         */
        $xs = $this->dict->get($key, array());
        return new GroupingGinq(IteratorUtil::iterator($xs), $key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function put($key, $value)
    {
        /** @var \ArrayObject $xs */
        if (!$this->dict->contains($key)) {
            $this->dict->put($key, new \ArrayObject());
        }
        $xs = $this->dict->get($key);
        $xs->append($value);
    }
}

