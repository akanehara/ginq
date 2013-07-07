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

namespace Ginq\Selector;

use Ginq\Accessor\Accessor;
use Ginq\Accessor\AccessorParser;
use Ginq\Core\Selector;

class PathSelector implements Selector
{
    static public function parse($path)
    {
        return new PathSelector(AccessorParser::parse($path, true));
    }

    /**
     * @param Accessor $accessor
     * @internal param array $names
     */
    public function __construct($accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @throws \DomainException
     * @return mixed
     */
    public function select($v, $k)
    {
        return $this->accessor->get($v);
    }
}

