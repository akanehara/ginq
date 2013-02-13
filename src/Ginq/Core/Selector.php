<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/12
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 */
namespace Ginq\Core;

interface Selector
{
    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return mixed
     */
    public function select($v, $k);
}
