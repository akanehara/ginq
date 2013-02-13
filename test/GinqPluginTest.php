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
    	$this->assertArrayHasKey('eachEager', $functions);
    	$this->assertEquals(array('SamplePlugin', 'eachEager'), $functions['eachEager']);
    }

    public function testUsePluginFunction() {
    	SamplePlugin::register();

    	$sum = 0;
    	Ginq::range(1, 10)
    		->eachEager(function ($v) use(&$sum) {
    			$sum += $v;
    		})
   		;

   		$this->assertEquals(55, $sum);    	
    }
}

class SamplePlugin {
	public static function register() {
		Ginq::register(get_called_class());
	}

	public static function eachEager(\Ginq $self, $selector) {
		if (is_null($selector)) {
			throw new \ArgumentException('must be passed closure as 2nd argument.');
		}

		foreach ($self as $k => $v) {
			$selector($v, $k);
		}
	}
}