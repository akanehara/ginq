<?php
/**
 * Created by JetBrains PhpStorm.
 * User: akanehara
 * Date: 13/02/14
 * Time: 1:01
 * To change this template use File | Settings | File Templates.
 */

namespace Ginq\Selector;

class PropertySelector implements \Ginq\Core\Selector
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
     * @throws \DomainException
     * @return mixed
     */
    public function select($v, $k)
    {
        if (is_array($v)) {
            return @$v[$this->name];
        } else if (is_object($v)) {
            return @$v->{$this->name};
        }
        $type = gettype($v);
        throw new \DomainException("'$type' object has no key or field");
    }
}

