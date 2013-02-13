<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 15:20
 * To change this template use File | Settings | File Templates.
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
