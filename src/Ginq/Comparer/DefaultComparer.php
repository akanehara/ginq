<?php
namespace Ginq\Comparer;

use Ginq\Core\Comparer;

class DefaultComparer implements Comparer
{
    /**
     * @param mixed $v0 - left value (sort key)
     * @param mixed $v1 - right value (sort key)
     * @param mixed $k0 - left key
     * @param mixed $k1 - right key
     * @return int
     */
    public function compare($v0, $v1, $k0, $k1)
    {
        if (is_string($v0) && is_string($v1)) {
            return strcmp($v0, $v1);
        }
        if (is_numeric($v0) && is_numeric($v1)) {
            if ($v0 == $v1) return 0;
            return ($v0 < $v1) ? -1 : 1;
        }
        return 0;
    }
}
