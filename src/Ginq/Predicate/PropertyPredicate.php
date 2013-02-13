<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/14
 * Time: 1:08
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Predicate;

class PropertyPredicate implements \Ginq\Core\Predicate
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $v value
     * @param mixed $k key
     * @return bool
     */
    public function predicate($v, $k)
    {
        if (is_array($v)) {
            return @$v[$this->name];
        } else if (is_object($v)) {
            return @$v->{$this->name};
        }
        $type = gettype($v);
        throw new DomainException("'$type' object has no key or field");
    }
}
