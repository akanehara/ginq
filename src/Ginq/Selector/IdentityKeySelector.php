<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 15:22
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Selector;

class IdentityKeySelector implements \Ginq\Core\Selector
{
    /**
     * @var IdentityKeySelector
     */
    static private $inst;

    /**
     * @return IdentityKeySelector
     */
    static public function getInstance() {
        if (is_null(self::$inst)) {
            self::$inst = new IdentityKeySelector();
        }
        return self::$inst;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return mixed
     */
    public function select($v, $k)
    {
        return $k;
    }
}
