<?php
require_once "PHPUnit/Framework/IncompleteTestError.php";
require_once dirname(dirname(__FILE__)) . "/src/Ginq.php";

class Person
{
    public $id;
    public $name;
    public $city;
    public function __construct($id, $name, $city)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }
}

/**
 * Test class for Ginq.
 */
class GinqTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite("GinqTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        Ginq::useIterator();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * testGetIterator().
     */
    public function testGetIterator()
    {
        $iter = Ginq::from(array(1,2,3,4,5))->getIterator();
        $this->assertTrue($iter instanceof Iterator);
        $arr = array();
        foreach ($iter as $x) {
            $arr[] = $x;
        }
        $this->assertEquals(array(1,2,3,4,5), $arr);
    }
    /**
     * testToArray().
     */
    public function testToArray()
    {
        $arr = Ginq::from(array(1,2,3,4,5))->toArray();
        $this->assertEquals(array(1,2,3,4,5), $arr);
    }

   /**
     * @todo Implement testAny().
     */
    public function testAny()
    {
        $this->assertTrue(
            Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
                ->any(function($x) { return 5 <= $x; })
        );

        $this->assertFalse(
            Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
                ->any(function($x) { return 100 <= $x; })
        );

        // infinite sequence
        $this->assertTrue(
            Ginq::range(1)->any(function($x) { return 5 <= $x; })
        );
    }

    /**
     * @todo Implement testAll().
     */
    public function testAll()
    {
        $this->assertTrue(
            Ginq::from(array(2,4,6,8,10))
                ->all(function($x) { return $x % 2 == 0; })
        );

        $this->assertFalse(
            Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
                ->all(function($x) { return $x < 10; })
        );

        // infinite sequence
        $this->assertFalse(
            Ginq::range(1)->all(function($x) { return $x <= 10; })
        );
    }

    /**
     * testZero().
     */
    public function testZero()
    {
        $arr = Ginq::zero()->toArray();
        $this->assertEquals(array(), $arr);
    }

   /**
     * testRange().
     */
    public function testRange()
    {
        // finite sequence
        $xs = Ginq::range(1,10)->toArray();
        $this->assertEquals(array(1,2,3,4,5,6,7,8,9,10), $xs);

        // finite sequence with step
        $xs = Ginq::range(1,10, 2)->toArray(); 
        $this->assertEquals(array(1,3,5,7,9), $xs);

        // finite sequence with negative step
        $xs = Ginq::range(0,-9, -1)->toArray(); 
        $this->assertEquals(array(0,-1,-2,-3,-4,-5,-6,-7,-8,-9), $xs);

        // infinite sequence
        $xs = Ginq::range(1)->take(10)->toArray(); 
        $this->assertEquals(array(1,2,3,4,5,6,7,8,9,10), $xs);

        // infinite sequence with step
        $xs = Ginq::range(10, null, 5)->take(5)->toArray(); 
        $this->assertEquals(array(10,15,20,25,30), $xs);

        // infinite sequence with negative step
        $xs = Ginq::range(-10, null, -5)->take(5)->toArray(); 
        $this->assertEquals(array(-10,-15,-20,-25,-30), $xs);

        // contradict range
        $xs = Ginq::range(1, -10, 1)->toArray(); 
        $this->assertEquals(array(), $xs);

        $xs = Ginq::range(1, 10, -1)->toArray(); 
        $this->assertEquals(array(), $xs);
    }

    /**
     * testRepeat().
     */
    public function testRepeat()
    {
        // infinite repeat
        $xs = Ginq::repeat("foo")->take(3)->toArray();
        $this->assertEquals(array("foo","foo","foo"), $xs);
    }

    /**
     * testCycle().
     */
    public function testCycle()
    {
        $data = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
        $xs = Ginq::cycle($data)->take(10)->toArray();
        $this->assertEquals(
            array('Mon','Tue','Wed','Thu','Fri','Sat','Sun','Mon','Tue','Wed'),
            $xs
        );
    }

    /**
     * testFrom().
     */
    public function testFrom()
    {
        // array
        $arr = Ginq::from(array(1,2,3,4,5))->toArray();
        $this->assertEquals(array(1,2,3,4,5), $arr);
        
        // Iterator
        $arr = Ginq::from(new ArrayIterator(array(1,2,3,4,5)))->toArray();
        $this->assertEquals(array(1,2,3,4,5), $arr);
        
        // IteratorAggregate
        $arr = Ginq::from(new ArrayObject(array(1,2,3,4,5)))->toArray();
        $this->assertEquals(array(1,2,3,4,5), $arr);

        // Ginq
        $arr = Ginq::from(Ginq::from(array(1,2,3,4,5)))->toArray();
        $this->assertEquals(array(1,2,3,4,5), $arr);
    }

    /**
     * testSelect().
     * 
     * @expectedException InvalidArgumentException
     */
    public function testSelect()
    {
        // selector function
        $xs = Ginq::from(array(1,2,3,4,5))
                   ->select(function($x) { return $x * $x; })
                   ->toArray();
        $this->assertEquals(array(1,4,9,16,25), $xs);

        // key selector string
        $data = array(
             array('id' => 1, 'name' => 'Taro',    'city' => 'Takatsuki')
            ,array('id' => 2, 'name' => 'Atsushi', 'city' => 'Ibaraki')
            ,array('id' => 3, 'name' => 'Junko',   'city' => 'Sakai')
        );
        $xs = Ginq::from($data)->select("name")->toArray();
        $this->assertEquals(array('Taro','Atsushi','Junko'), $xs);

        // field selector string
        $data = array(
             new Person(1, 'Taro',    'Takatsuki')
            ,new Person(2, 'Atsushi', 'Ibaraki')
            ,new Person(3, 'Junko',   'Sakai')
        );
        $xs = Ginq::from($data)->select("name")->toArray();
        $this->assertEquals(array('Taro','Atsushi','Junko'), $xs);

        // invalid selector
        Ginq::from(array(1,2,3,4,5))->select(8); 
    }

    /**
     * testWhere().
     */
    public function testWhere()
    {
        $xs = Ginq::from(array(1,2,3,4,5,6,7,8,9,10))
            ->where(function($x) { return ($x % 2) == 0; })
            ->toArray();
        $this->assertEquals(array(2,4,6,8,10), $xs);
    }

    /**
     * testTake().
     */
    public function testTake()
    {
        $xs = Ginq::from(array(1,2,3,4,5,6,7,8,9))->take(5)->toArray();
        $this->assertEquals(array(1,2,3,4,5), $xs);
    }

    /**
     * testDrop().
     */
    public function testDrop()
    {
        $xs = Ginq::from(array(1,2,3,4,5,6,7,8,9))->drop(5)->toArray();
        $this->assertEquals(array(6,7,8,9), $xs);
    }

    /**
     * @todo Implement testTakeWhile().
     */
    public function testTakeWhile()
    {
        $xs = Ginq::from(array(1,2,3,4,5,6,7,8,9,8,7,6,5,4,3,2,1))
            ->takeWhile(function($x) { return $x <= 5; })
            ->toArray();
        $this->assertEquals(array(1,2,3,4,5), $xs);
    }

    /**
     * @todo Implement testDropWhile().
     */
    public function testDropWhile()
    {
        $xs = Ginq::from(array(1,2,3,4,5,6,7,8,9,8,7,6,5,4,3,2,1))
            ->dropWhile(function($x) { return $x <= 5; })
            ->toArray();
        $this->assertEquals(array(6,7,8,9,8,7,6,5,4,3,2,1), $xs);
    }

     /**
     * @todo Implement testConcat().
     */
    public function testConcat()
    {
        $xs = Ginq::from(array(1,2,3,4,5))->concat(array(6,7,8,9))->toArray();
        $this->assertEquals(array(1,2,3,4,5,6,7,8,9), $xs);
    }

    /**
     * testSelectMany().
     */
    public function testSelectMany()
    {
        $phoneBook = array(
            array(
                'name'   => 'Taro',
                'phones' => array(
                    '03-1234-5678',
                    '090-8421-9061'
                )
            ),
            array(
                'name'   => 'Atsushi',
                'phones' => array(
                    '050-1198-4458'
                )
            ), 
            array(
                'name'   => 'Junko',
                'phones' => array(
                    '06-1111-3333',
                    '090-9898-1314',
                    '050-6667-2231'
                )
            )
         );

        // without join selector
        $phones = Ginq::from($phoneBook)->selectMany('phones')->toArray();
        $this->assertEquals(array(
            '03-1234-5678', '090-8421-9061',
            '050-1198-4458', '06-1111-3333',
            '090-9898-1314', '050-6667-2231'
        ), $phones);

        // with join selector
        $phones = Ginq::from($phoneBook)
            ->selectMany(
                'phones',
                function($person, $phone) {
                    return "${person['name']} : $phone";
                }
            )->toArray();
        $this->assertEquals(array(
            'Taro : 03-1234-5678',
            'Taro : 090-8421-9061',
            'Atsushi : 050-1198-4458',
            'Junko : 06-1111-3333',
            'Junko : 090-9898-1314',
            'Junko : 050-6667-2231'
        ), $phones);
    }

    /**
     * testJoin().
     */
    public function testJoin()
    {
        $persons = array(
             array('id' => 1, 'name' => 'Taro')
            ,array('id' => 2, 'name' => 'Atsushi')
            ,array('id' => 3, 'name' => 'Junko')
        );

        $phones = array(
             array('id' => 1, 'owner' => 1, 'phone' => '03-1234-5678')
            ,array('id' => 2, 'owner' => 1, 'phone' => '090-8421-9061')
            ,array('id' => 3, 'owner' => 2, 'phone' => '050-1198-4458')
            ,array('id' => 4, 'owner' => 3, 'phone' => '06-1111-3333')
            ,array('id' => 5, 'owner' => 3, 'phone' => '090-9898-1314')
            ,array('id' => 6, 'owner' => 3, 'phone' => '050-6667-2231')
        );

        // key selector string
        $xs = Ginq::from($persons)->join($phones,
            'id', 'owner',
            function($outer, $inner) {
                return array($outer['name'], $inner['phone']);
            }
        )->toArray();
        $this->assertEquals(
            array(
                 array('Taro', '03-1234-5678')
                ,array('Taro', '090-8421-9061')
                ,array('Atsushi', '050-1198-4458')
                ,array('Junko', '06-1111-3333')
                ,array('Junko', '090-9898-1314')
                ,array('Junko', '050-6667-2231')
            ), $xs
        );

        // key selector function
        $xs = Ginq::from($persons)->join($phones,
            function($outer) { return $outer['id']; },
            function($inner) { return $inner['owner']; },
            function($outer, $inner) {
                return array($outer['name'], $inner['phone']);
            }
        )->toArray();

        $this->assertEquals(
            array(
                 array('Taro', '03-1234-5678')
                ,array('Taro', '090-8421-9061')
                ,array('Atsushi', '050-1198-4458')
                ,array('Junko', '06-1111-3333')
                ,array('Junko', '090-9898-1314')
                ,array('Junko', '050-6667-2231')
            ), $xs
        );
    }

    /**
     * testZip().
     */
    public function testZip()
    {
        $xs = Ginq::cycle(array("red", "green"))->zip(Ginq::range(1, 8),
            function($c, $n) { return "$n - $c"; }
        )->toArray();
        $this->assertEquals(array(
            "1 - red", "2 - green",
            "3 - red", "4 - green",
            "5 - red", "6 - green",
            "7 - red", "8 - green"
        ), $xs);
    }

    /**
     * testGroupBy().
     */
    public function testGroupBy()
    {
        $phones = array(
             array('id' => 1, 'owner' => 1, 'phone' => '03-1234-5678')
            ,array('id' => 2, 'owner' => 1, 'phone' => '090-8421-9061')
            ,array('id' => 3, 'owner' => 2, 'phone' => '050-1198-4458')
            ,array('id' => 4, 'owner' => 3, 'phone' => '06-1111-3333')
            ,array('id' => 5, 'owner' => 3, 'phone' => '090-9898-1314')
            ,array('id' => 6, 'owner' => 3, 'phone' => '050-6667-2231')
        );
        $xss = Ginq::from($phones)->groupBy(
            function($x) { return $x['owner']; },
            function($x) { return $x['phone']; }
        )->toArrayRec();
        $this->assertEquals(array(
            array('03-1234-5678', '090-8421-9061'),
            array('050-1198-4458'),
            array('06-1111-3333', '090-9898-1314', '050-6667-2231')
        ), $xss);
    }
 }

// Call GinqTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "GinqTest::main") {
    GinqTest::main();
}
?>
