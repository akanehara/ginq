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

namespace Ginq\Lambda;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Lambda
{
    /**
     * @var ExpressionLanguage
     */
    static protected $exprLang;

    /**
     * @return ExpressionLanguage
     */
    static protected function getExpressionLanguage()
    {
        if (is_null(static::$exprLang)) {
            static::$exprLang = new ExpressionLanguage();
        }
        return static::$exprLang;
    }

    /**
     * @param string $src
     * @throws SyntaxError
     * @return array
     */
    static protected function parseNames($src)
    {
        $pattern = '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*(.*)/A';

        $rest = ltrim($src);

        if ($rest === "") {
            return array();
        }

        if (!preg_match($pattern, $rest, $m)) {
            $pos = strlen($src) - strlen($rest) + 1;
            throw new SyntaxError("Unexpected token in '$src' on position $pos");
        }
        list($_, $name, $rest) = $m;
        $names = array($name);

        if ($rest === "") {
            return $names;
        }

        while ($rest !== "") {
            if (!preg_match('/\,\s*(\S.*)/A', $rest, $m)) {
                $pos = strlen($src) - strlen($rest) + 1;
                throw new SyntaxError("Unexpected token in '$src' on position $pos");
            }
            list($_, $rest) = $m;
            if (!preg_match($pattern, $rest, $m)) {
                $pos = strlen($src) - strlen($rest) + 1;
                throw new SyntaxError("Unexpected token in '$src' on position $pos");
            }
            list($_, $name, $rest) = $m;
            $names[] = $name;
        }

        return $names;
    }

    /**
     * @param array $lambda ex: ['x, y' => 'x + y', 'z' => $z]
     * @throws \Ginq\Lambda\SyntaxError|\InvalidArgumentException
     * @return callable
     */
    static public function fun($lambda)
    {
        if (!is_array($lambda)) {
            $t = gettype($lambda);
            throw new \InvalidArgumentException("Lambda::fun expected array, got $t.");
        }
        if (empty($lambda)) {
            throw new \InvalidArgumentException("Lambda::fun expected at least 1 element, got 0.");
        }
        $names = static::parseNames(key($lambda));
        $expr  = array_shift($lambda);
        $env   = $lambda;
        $lang = static::getExpressionLanguage();
        try {
            $lang->parse($expr, array_merge($names, array_keys($env)));
        } catch (\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
            throw new \Ginq\Lambda\SyntaxError($e->getMessage(), $e);
        }
        return function() use ($lang, $names, $expr, $env) {
            $args = func_get_args();
            $params = array(); $i = 0;
            foreach ($names as $name) {
                $params[$name] = @$args[$i++];
            }
            try {
                return $lang->evaluate($expr, array_merge($params, $env));
            } catch (\Symfony\Component\ExpressionLanguage\SyntaxError $e) {
                throw new \Ginq\Lambda\SyntaxError($e->getMessage(), $e);
            }
        };
    }
}
