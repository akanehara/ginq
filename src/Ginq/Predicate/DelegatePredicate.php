<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/14
 * Time: 0:03
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Predicate;

class DelegatePredicate implements \Ginq\Core\Predicate
{
    /**
     * @var \Closure
     */
    private $func;

    public function __construct($func)
    {
        $this->func = $func;
    }

    /**
     * @param mixed $v
     * @param mixed $k
     * @return bool
     */
    public function predicate($v, $k)
    {
        $f = $this->func;
        return $f($v, $k);
    }
}

