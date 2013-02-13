<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 16:29
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Core;

interface JoinSelector
{
    /**
     * @param mixed $v0 value0
     * @param mixed $v1 value1
     * @param mixed $k0 key0
     * @param mixed $k1 key1
     * @return mixed selected
     */
    public function joinSelect($v0, $v1, $k0, $k1);
}

