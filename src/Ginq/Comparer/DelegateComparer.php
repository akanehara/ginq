<?php
namespace Ginq\Comparer;

use Ginq\Core\Comparer;

class DelegateComparer implements Comparer
{
    /**
     * @var \Closure
     */
    private $fn;

    /**
     * @param \Closure $fn
     */
    public function __construct($fn)
    {
        $this->fn = $fn;
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
        $fn = $this->fn;
        return $fn($v0, $v1, $k0, $k1);
    }
}

