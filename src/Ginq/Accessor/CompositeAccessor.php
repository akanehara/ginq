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

namespace Ginq\Accessor;


class CompositeAccessor implements Accessor {

    /**
     * @var Accessor
     */
    protected $first;

    /**
     * @var Accessor
     */
    protected $second;

    /**
     * @param Accessor $first
     * @param Accessor $second
     */
    public function __construct($first, $second)
    {
        $this->first  = $first;
        $this->second = $second;
    }

    /**
     * @param mixed $x
     * @throws \RuntimeException
     * @return mixed
     */
    public function get($x)
    {
        return $this->second->get($this->first->get($x));
    }
}
