<?php
require_once "PHPUnit/Framework/IncompleteTestError.php";
require_once dirname(dirname(__FILE__)) . "/src/Ginq.php";

class GinqPluginTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite("GinqPluginTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }	

    public function testResiterPlugin() {
    	SamplePlugin::register();

    	$functions = Ginq::listRegisterdFunctions();

    	$this->assertCount(1, $functions);
    	$this->assertArrayHasKey('each', $functions);
    	$this->assertEquals(array('SamplePlugin', 'each'), $functions['each']);
    }

    // public function testUsePluginFunction() {
    // 	SamplePlugin::register();

    // 	$sum = 0;
    // 	Ginq::range(1, 10)
    // 		->foreach(function ($v) {
    // 			$sum += $v;
    // 		})
   	// 	;

   	// 	$this->assertEquals(55, $sum);    	
    // }
}

class SamplePlugin {
	public static function register() {
		Ginq::register(get_called_class());
	}

	public static function each(\Iterator $iter, $selector) {

	}
}