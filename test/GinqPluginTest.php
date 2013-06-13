<?php

require_once dirname(dirname(__FILE__)) . "/src/Ginq.php";

use Ginq\Ginq;

class GinqPluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite("GinqPluginTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testResiterPlugin()
    {
        SamplePlugin::register();

        $functions = Ginq::listRegisteredFunctions();

        $this->assertCount(1, $functions);
        $this->assertArrayHasKey('eachEager', $functions);
        $this->assertEquals(array('SamplePlugin', 'eachEager'), $functions['eachEager']);
    }

    public function testUsePluginFunction()
    {
        SamplePlugin::register();

        $sum = 0;
        Ginq::range(1, 10)->eachEager(function ($v) use(&$sum) {
            $sum += $v;
        });
        $this->assertEquals(55, $sum);

        $group_count = 0;
        Ginq::range(1, 299)
            ->groupBy(function($x) { return floor($x / 100); })
            ->eachEager(function ($gr, $k) use(&$group_count) {
                $group_count += 1;
            });
        $this->assertEquals(3, $group_count);
    }
}

class SamplePlugin
{
    public static function register()
    {
        Ginq::register(get_called_class());
    }

    public static function eachEager(Ginq $self, $selector) {
        if (is_null($selector)) {
            throw new \InvalidArgumentException('must be passed closure as 2nd argument.');
        }

        foreach ($self as $k => $v) {
            $selector($v, $k);
        }
    }
}