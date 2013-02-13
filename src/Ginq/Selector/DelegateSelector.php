<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/12
 * Time: 14:04
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Selector;

class DelegateSelector implements \Ginq\Core\Selector
{
    /**
     * @var callable
     */
    private $func;

    /**
     * @param \Closure $func  ($v, $k)
     */
    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return mixed
     */
    public function select($v, $k)
    {
        $f = $this->func;
        return $f($v, $k);
    }
}

