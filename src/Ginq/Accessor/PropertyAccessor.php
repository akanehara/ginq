<?php
/**
 * Ginq: `LINQ to Object` inspired DSL for PHP
 * Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP Version 5.3 or later
 *
 * @author     Atsushi Kanehara <akanehara@gmail.com>
 * @copyright  Copyright 2013, Atsushi Kanehara <akanehara@gmail.com>
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package    Ginq
 */

namespace Ginq\Accessor;

/**
 * Class PropertyAccessor
 *
 * This code references:
 * Symfony\Component\PropertyAccess::readProperty
 * https://github.com/symfony/PropertyAccess/blob/master/PropertyAccessor.php
 * Copyright (c) 2004-2013 Fabien Potencier
 *
 * @package Ginq\Accessor
 */
class PropertyAccessor implements Accessor {

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $x
     * @throws \RuntimeException
     * @return mixed
     */
    public function get($x)
    {
        $name = $this->name;

        if (!is_object($x)) {
            $type = gettype($x);
            throw new \RuntimeException("Unable to read property '$name' from '$type'");
        }

        $upperCamelName = self::toUpperCamelCase($name);
        $getter    = 'get' . $upperCamelName;
        $isser     = 'is'  . $upperCamelName;
        $hasser    = 'has' . $upperCamelName;

        $refl = new \ReflectionClass($x);
        $hasProperty = $refl->hasProperty($name);

        if ($refl->hasMethod($getter) && $refl->getMethod($getter)->isPublic()) {
            return $x->$getter();
        } else if ($refl->hasMethod($isser) && $refl->getMethod($isser)->isPublic()) {
            return $x->$isser();
        } else if ($refl->hasMethod($hasser) && $refl->getMethod($hasser)->isPublic()) {
            return $x->$hasser();
        } else if ($refl->hasMethod('__get') && $refl->getMethod('__get')->isPublic()) {
            return $x->$name;
        } else if ($hasProperty && $refl->getProperty($name)->isPublic()) {
            return $x->$name;
        } else if (!$hasProperty && property_exists($x, $name)) {
            return $x->$name;
        } else if ($refl->hasMethod('__call') && $refl->getMethod('__call')->isPublic()) {
            return $x->$getter();
        } else {
            throw new \RuntimeException(
                "Neither the property '$name' nor one of the methods '$getter()', ".
                "'$isser()', '$hasser()', '__get()' or '__call()' exist and have public access in ".
                "class '{$refl->name}'."
            );
        }
    }

    static protected function toUpperCamelCase($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/',
            function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); },
            $string
        );
    }
}