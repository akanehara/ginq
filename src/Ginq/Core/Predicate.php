<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 16:31
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Core;

interface Predicate
{
    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return bool
     */
    public function predicate($v, $k);
}
