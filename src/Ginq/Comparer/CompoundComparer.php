<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/19
 * Time: 13:26
 * To change this template use File | Settings | File Templates.
 */
namespace Ginq\Comparer;

use Ginq\Core\Comparer;

class CompoundComparer implements Comparer
{
    /**
     * @var Comparer
     */
    protected $primary;

    /**
     * @var Comparer
     */
    protected $secondary;

    /**
     * @param Comparer $primary
     * @param Comparer $secondary
     */
    public function __construct($primary, $secondary)
    {
        $this->primary = $primary;
        $this->secondary = $secondary;
    }

    /**
     * @param mixed $v0 - left value (sort key)
     * @param mixed $v1 - right value (sort key)
     * @param mixed $k0 - left key
     * @param mixed $k1 - right key
     * @return int
     */
    public function compare($v0, $v1, $k0, $k1)
    {
        $lhs = $this->primary->compare($v0, $v1, $k0, $k1);
        if ($lhs === 0) {
            return $this->secondary->compare($v0, $v1, $k0, $k1);
        } else {
            return $lhs;
        }
    }
}

