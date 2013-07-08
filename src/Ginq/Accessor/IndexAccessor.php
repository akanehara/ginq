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

/**
 * Class IndexAccessor
 *
 * This code references:
 * Symfony\Component\PropertyAccess::readProperty
 * https://github.com/symfony/PropertyAccess/blob/master/PropertyAccessor.php
 * Copyright (c) 2004-2013 Fabien Potencier
 *
 * @package Ginq\Accessor
 */
class IndexAccessor implements Accessor {
    /**
     * @var int|string
     */
    protected $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    /**
     * @param mixed $x
     * @throws \RuntimeException
     * @return mixed
     */
    public function get($x)
    {
        if (!$x instanceof \ArrayAccess && !is_array($x)) {
            $type = gettype($x);
            $index = $this->index;
            throw new \RuntimeException(
                "Index '{$index}' cannot be read from object of type '$type' because it doesn't implement \\ArrayAccess"
            );
        }
        return $x[$this->index];
    }
}

