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

use Ginq\Iterator\RenumIterator;

class Set
{
    /**
     * @var EqualityComparer
     */
    protected $eqComparer;

    /**
     * @var array
     */
    protected $elements;

    /**
     * @param $eqComparer
     * @param \Traversable $elements
     * @internal param \Ginq\Core\EqualityComparer $equalityComparer
     */
    public function __construct($eqComparer, $elements=null)
    {
        $this->eqComparer = $eqComparer;
        $this->elements = array();
        if (!is_null($elements)) {
            foreach ($elements as $e) {
                $this->add($e);
            }
        }
    }

    /**
     * @param mixed $x
     * @return bool
     */
    public function contains($x)
    {
        $hash = $this->eqComparer->hash($x);
        return array_key_exists($hash, $this->elements);
    }

    /**
     * @param mixed $x
     * @return bool
     */
    public function add($x)
    {
        $hash = $this->eqComparer->hash($x);
        if (array_key_exists($hash, $this->elements)) {
            return false;
        } else {
            $this->elements[$hash] = $x;
            return true;
        }
    }

    /**
     * @param mixed $x
     * @return bool
     */
    public function remove($x)
    {
        $hash = $this->eqComparer->hash($x);
        if (array_key_exists($hash, $this->elements)) {
            unset($this->elements[$hash]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return new RenumIterator($this->elements);
    }
}
