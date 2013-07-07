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

class AccessorParser {

    /**
     * @param $src
     * @throws \RuntimeException
     * @return Accessor
     */
    static public function parse($src)
    {
        // first
        if (preg_match('/^((\w+)|\[([^\]]+)\])(.*)/', $src, $match)) {
            list($_, $_, $property, $index, $rest) = $match;
            $accessor = self::createAccessor($property, $index);
        } else {
            throw new \InvalidArgumentException("invalid property path '$src'.");
        }

        if ($rest === "") {
            return $accessor;
        }

        // rest
        while (preg_match('/^(\.(\w+)|\[([^\]]+)\])(.*)/', $rest, $match)) {
            list($_, $_, $property, $index, $rest) = $match;
            $accessor = new CompositeAccessor($accessor, self::createAccessor($property, $index));
        }
        if ($rest !== "") {
            throw new \InvalidArgumentException("invalid property path '$src'.");
        }

        return $accessor;
    }

    /**
     * @param string $property
     * @param string $index
     * @return Accessor
     */
    static protected function createAccessor($property, $index)
    {
        if ($property !== "") {
            return new PropertyAccessor($property);
        } else {
            return new IndexAccessor($index);
        }
    }
}

