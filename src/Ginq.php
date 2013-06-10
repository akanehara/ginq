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

use Ginq\GinqContext;

spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    if ($class == 'Ginq') {
        include __DIR__ . DIRECTORY_SEPARATOR . 'Ginq.php';
        return; // Ginq class itself.
    }
    $pos = strpos($class, '\\');
    if ($pos === false) {
        return; // Not in namespace.
    }
    $rootNS = substr($class, 0, $pos);
    if ($rootNS != 'Ginq') {
        return; // Not in Ginq namespace.
    }
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    include __DIR__ . DIRECTORY_SEPARATOR . $classPath;
});

/**
 * Ginq
 */
class Ginq extends GinqContext { }

