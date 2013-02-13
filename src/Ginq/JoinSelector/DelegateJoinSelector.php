<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/13
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\JoinSelector;

class DelegateJoinSelector implements \Ginq\Core\JoinSelector
{
    /**
     * @var callable
     */
    private $func;

    /**
     * @param \Closure $func
     */
    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * @param mixed $v0
     * @param mixed $v1
     * @param mixed $k0
     * @param mixed $k1
     * @return mixed
     */
    public function joinSelect($v0, $v1, $k0, $k1)
    {
        $f = $this->func;
        return $f($v0, $v1, $k0, $k1);
    }
}
