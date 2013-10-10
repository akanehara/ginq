<?php
require_once dirname(__DIR__) . "/src/Ginq/Accessor/Accessor.php";
require_once dirname(__DIR__) . "/src/Ginq/Accessor/IndexAccessor.php";
require_once dirname(__DIR__) . "/src/Ginq/Accessor/PropertyAccessor.php";
require_once dirname(__DIR__) . "/src/Ginq/Accessor/CompositeAccessor.php";
require_once dirname(__DIR__) . "/src/Ginq/Accessor/AccessorParser.php";

use \Ginq\Accessor\AccessorParser;

class AccessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        $suite = new PHPUnit_Framework_TestSuite("AccessorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * testGetIndex().
     */
    public function testGetIndex()
    {
        $x = array("bowwow", "meow");
        $this->assertEquals("meow", AccessorParser::parse('[1]')->get($x));

        $x = array("dog"=>"bowwow", "cat"=>"meow");
        $this->assertEquals("meow", AccessorParser::parse('[cat]')->get($x));
    }

    /**
     * testGetNestedIndex().
     */
    public function testGetNestedIndex()
    {
        $x = array(array("bowwow", "meow"));
        $this->assertEquals("meow", AccessorParser::parse('[0][1]')->get($x));

        $x = array(array("dog"=>"bowwow", "cat"=>"meow"));
        $this->assertEquals("meow", AccessorParser::parse('[0][cat]')->get($x));
    }

    /**
     * testGetProperty().
     */
    public function testGetProperty()
    {
        $x = (object) array("dog"=>"bowwow", "cat"=>"meow");
        $this->assertEquals("meow", AccessorParser::parse('cat')->get($x));
    }

    /**
     * testGetNestedProperty().
     */
    public function testGetNestedProperty()
    {
        $x = (object) array("pet" => (object) array("dog"=>"bowwow", "cat"=>"meow"));
        $this->assertEquals("meow", AccessorParser::parse('pet.cat')->get($x));
    }

    /**
     * testGetter().
     */
    public function testGetter()
    {
        $x = new Entity("non-no");
        $this->assertEquals("non-no", AccessorParser::parse('name')->get($x));
    }

    /**
     * testIsser().
     */
    public function testIsser()
    {
        $x = new Entity("non-no");
        $x->setEnable(false);
        $this->assertFalse(AccessorParser::parse('enable')->get($x));
    }

    /**
     * testHasser().
     */
    public function testHasser()
    {
        $x = new Entity("non-no");
        $this->assertFalse(AccessorParser::parse('child')->get($x));

        $x->addChild(new Entity("mint"));
        $this->assertTrue(AccessorParser::parse('child')->get($x));
    }

    /**
     * testPropertyNotFound().
     */
    public function testPropertyNotFound()
    {
        $x = new Entity("non-no");
        try {
            $this->assertEquals("non-no", AccessorParser::parse('height')->get($x));
        } catch (RuntimeException $e) {

        }
    }

    /**
     * testMagicGet().
     */
    public function testMagicGet()
    {
        $x = new MagicGetEntity(array("age"=>3));
        $this->assertEquals(3, AccessorParser::parse('age')->get($x));

        try {
            $this->assertEquals(3.5, AccessorParser::parse('weight')->get($x));
            $this->fail();
        } catch (RuntimeException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * testMagicCall().
     */
    public function testMagicCall()
    {
        $x = new MagicCallEntity();
        $this->assertEquals("getFoo", AccessorParser::parse("foo")->get($x));
    }
}



class Entity {

    private $name;
    private $enabled;
    private $children;

    public function __construct($name)
    {
        $this->name     = $name;
        $this->enabled  = true;
        $this->children = array();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function isEnable()
    {
        return $this->enabled;
    }

    public function setEnable($enabled)
    {
        $this->enabled = $enabled;
    }

    public function hasChild()
    {
        return count($this->children) > 0;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function addChild($e)
    {
        $this->children[] = $e;
    }


}

class MagicGetEntity {
    public function __construct($data=array())
    {
        $this->data     = $data;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        } else {
            return null;
        }
    }
}

class MagicCallEntity {
    public function __call($name, $arguments)
    {
        return $name;
    }
}
