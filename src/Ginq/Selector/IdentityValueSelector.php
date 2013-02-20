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

class IdentityValueSelector implements \Ginq\Core\Selector
{
    /**
     * @var IdentityValueSelector
     */
    static private $inst;

    /**
     * @return IdentityValueSelector
     */
    static public function getInstance() {
        if (is_null(self::$inst)) {
            self::$inst = new IdentityValueSelector();
        }
        return self::$inst;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return mixed selected
     */
    public function select($v, $k)
    {
        return $v;
    }
}
