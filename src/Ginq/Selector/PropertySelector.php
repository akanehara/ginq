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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class PropertySelector implements Selector
{
    /**
     * @var PropertyAccessor
     */
    static protected $accessor;

    /**
     * @return PropertyAccessor
     */
    static protected function getAccessotr()
    {
        if (is_null(static::$accessor)) {
            static::$accessor = PropertyAccess::createPropertyAccessor();
        }
        return static::$accessor;
    }

    /**
     * @var PropertyPathInterface
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = new PropertyPath($path);
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return mixed
     */
    public function select($v, $k)
    {
        $accessor = $this->getAccessotr();
        return $accessor->getValue($v, $this->path);
    }
}
