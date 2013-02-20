<?php
namespace Ginq\Comparer;

use Ginq\Core\Comparer;

class ReverseComparer implements Comparer
{
    /**
     * @var Comparer
     */
    protected $comparer;

    /**
     * @param Comparer $comparer
     */
    public function __construct($comparer)
    {
        $this->comparer = $comparer;
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
        // flip
        return $this->comparer->compare($v1, $v0, $k1, $k0);
    }
}
