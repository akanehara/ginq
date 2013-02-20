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

namespace Ginq\Predicate;

class PropertyPredicate implements \Ginq\Core\Predicate
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @throws \DomainException
     * @return bool
     */
    public function predicate($v, $k)
    {
        if (is_array($v)) {
            return @$v[$this->name];
        } else if (is_object($v)) {
            return @$v->{$this->name};
        }
        $type = gettype($v);
        throw new \DomainException("'$type' object has no key or field");
    }
}
