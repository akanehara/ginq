<?php
require_once dirname(dirname(__FILE__)) . "/src/Ginq/Core/Dictionary.php";
require_once dirname(dirname(__FILE__)) . "/src/Ginq/Core/EqualityComparer.php";

use \Ginq\Core\Dictionary;

class DictionaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite("DictionaryTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testScalarKeys()
    {
        $dict = new Dictionary();
        $dict->put(1, "1:int");
        $dict->put("1", "one:string");
        $dict->put(2, "2:int");
        $dict->put(null, "null:null");
        $dict->put(0, "0:int");

        $this->assertEquals("1:int",      $dict->get(1));
        $this->assertEquals("one:string", $dict->get("1"));
        $this->assertEquals("2:int",      $dict->get(2));
        $this->assertEquals("null:null",  $dict->get(null));
        $this->assertEquals("0:int",      $dict->get(0));
    }

    public function testObjectKeys()
    {
        $josephine  = new Rabbit("Josephine");
        $flopsy     = new Rabbit("Flopsy");
        $mopsy      = new Rabbit("Mopsy");
        $cottontail = new Rabbit("Cotton-tail");

        $josephine->addChild($flopsy);
        $josephine->addChild($mopsy);
        $josephine->addChild($cottontail);

        $dict = new Dictionary();
        $dict->put($josephine, "josephine");
        $dict->put($flopsy,    "flopsy");
        $dict->put($mopsy,     "mopsy");
        $dict->put($cottontail,"cottontail");

        $this->assertEquals("josephine",  $dict->get($josephine));
        $this->assertEquals("flopsy",     $dict->get($flopsy));
        $this->assertEquals("mopsy",      $dict->get($mopsy));
        $this->assertEquals("cottontail", $dict->get($cottontail));
    }
}

class Rabbit
{
    public $mother;
    public $children;

    public function __construct($name)
    {
        $this->name = $name;
        $this->children = array();
    }

    public function addChild($child)
    {
        $this->children[] = $child;
        $child->mother = $this;
    }
}

