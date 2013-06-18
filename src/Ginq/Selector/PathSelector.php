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

use Ginq\Core\Selector;

class PathSelector implements Selector
{
    static public function parse($path)
    {
        if (!preg_match('/^[^\/]+(\/[^\/]+)*$/', $path)) {
            throw new \InvalidArgumentException("invalid path '$path'");
        }
        $names = preg_split('/\//', $path);
        return new PathSelector($names);
    }

    /**
     * @param array $names
     */
    protected function __construct($names)
    {
        $this->names = $names;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @throws \DomainException
     * @return mixed
     */
    public function select($v, $k)
    {
        foreach ($this->names as $name) {
            if (is_array($v)) {
                $v = @$v[$name];
            } else if (is_object($v)) {
                $v = @$v->{$name};
            } else {
                $type = gettype($v);
                throw new \DomainException("'$type' has no key or field");
            }
        }
        return $v;
    }
}

