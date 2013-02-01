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
        $iter = Ginq::from([1,2,3,4,5])->getIterator();
        $this->assertTrue($iter instanceof Iterator);
        $arr = [];
        foreach ($iter as $x) {
            $arr[] = $x;
        }
        $this->assertEquals([1,2,3,4,5], $arr);
    }

    /**
     * testToArray().
     */
    public function testToArray()
    {
        $arr = Ginq::from([1,2,3,4,5])->toArray();
        $this->assertEquals([1,2,3,4,5], $arr);
    }

   /**
     * @todo Implement testAny().
     */
    public function testAny()
    {
        // Remove the following line when you implement this test.
        throw new PHPUnit_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testAll().
     */
    public function testAll()
    {
        // Remove the following line when you implement this test.
        throw new PHPUnit_Framework_IncompleteTestError;
    }

    /**
     * testZero().
     */
    public function testZero()
    {
        $arr = Ginq::zero()->toArray();
        $this->assertEquals([], $arr);
    }

   /**
     * testRange().
     */
    public function testRange()
    {
        // finite sequence
        $xs = Ginq::range(1,10)->toArray();
        $this->assertEquals([1,2,3,4,5,6,7,8,9,10], $xs);

        // finite sequence with step
        $xs = Ginq::range(1,10, 2)->toArray(); 
        $this->assertEquals([1,3,5,7,9], $xs);

        // finite sequence with negative step
        $xs = Ginq::range(0,-9, -1)->toArray(); 
        $this->assertEquals([0,-1,-2,-3,-4,-5,-6,-7,-8,-9], $xs);

        // infinite sequence
        $xs = Ginq::range(1)->take(10)->toArray(); 
        $this->assertEquals([1,2,3,4,5,6,7,8,9,10], $xs);

        // infinite sequence with step
        $xs = Ginq::range(10, null, 5)->take(5)->toArray(); 
        $this->assertEquals([10,15,20,25,30], $xs);

        // infinite sequence with negative step
        $xs = Ginq::range(-10, null, -5)->take(5)->toArray(); 
        $this->assertEquals([-10,-15,-20,-25,-30], $xs);

        // contradict range
        $xs = Ginq::range(1, -10, 1)->toArray(); 
        $this->assertEquals([], $xs);

        $xs = Ginq::range(1, 10, -1)->toArray(); 
        $this->assertEquals([], $xs);
    }

    /**
     * testRepeat().
     */
    public function testRepeat()
    {
        // infinite repeat
        $xs = Ginq::repeat("foo")->take(3)->toArray();
        $this->assertEquals(["foo","foo","foo"], $xs);
    }

    /**
     * testCycle().
     */
    public function testCycle()
    {
        $data = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $xs = Ginq::cycle($data)->take(10)->toArray();
        $this->assertEquals(
            ['Mon','Tue','Wed','Thu','Fri','Sat','Sun','Mon','Tue','Wed'],
            $xs
        );
    }

    /**
     * testFrom().
     */
    public function testFrom()
    {
        // array
        $arr = Ginq::from([1,2,3,4,5])->toArray();
        $this->assertEquals([1,2,3,4,5], $arr);
        
        // Iterator
        $arr = Ginq::from(new ArrayIterator([1,2,3,4,5]))->toArray();
        $this->assertEquals([1,2,3,4,5], $arr);
        
        // IteratorAggregate
        $arr = Ginq::from(new ArrayObject([1,2,3,4,5]))->toArray();
        $this->assertEquals([1,2,3,4,5], $arr);

        // Ginq
        $arr = Ginq::from(Ginq::from([1,2,3,4,5]))->toArray();
        $this->assertEquals([1,2,3,4,5], $arr);
    }

    /**
     * testSelect().
     * 
     * @expectedException InvalidArgumentException
     */
    public function testSelect()
    {
        // selector function
        $arr = Ginq::from([1,2,3,4,5])
                   ->select(function($x) {return $x * $x; })
                   ->toArray();
        $this->assertEquals([1,4,9,16,25], $arr);

        // key selector string
        $data =
            [['id' => 1, 'name' => 'Taro',    'city' => 'Takatsuki']
            ,['id' => 2, 'name' => 'Atsushi', 'city' => 'Ibaraki']
            ,['id' => 3, 'name' => 'Junko',   'city' => 'Sakai']
            ];
        $arr = Ginq::from($data)->select("name")->toArray();
        $this->assertEquals(['Taro','Atsushi','Junko'], $arr);

        // field selector string
        $data =
            [new Person(1, 'Taro',    'Takatsuki')
            ,new Person(2, 'Atsushi', 'Ibaraki')
            ,new Person(3, 'Junko',   'Sakai')
            ];
        $arr = Ginq::from($data)->select("name")->toArray();
        $this->assertEquals(['Taro','Atsushi','Junko'], $arr);

        // invalid selector
        Ginq::from([1,2,3,4,5])->select(8); 
    }

    /**
     * testWhere().
     */
    public function testWhere()
    {
        $xs = Ginq::from([1,2,3,4,5,6,7,8,9,10])
            ->where(function($x) { return ($x % 2) == 0;})
            ->toArray();
        $this->assertEquals([2,4,6,8,10], $xs);
    }

    /**
     * testTake().
     */
    public function testTake()
    {
        $xs = Ginq::from([1,2,3,4,5,6,7,8,9])->take(5)->toArray();
        $this->assertEquals([1,2,3,4,5], $xs);
    }

    /**
     * testDrop().
     */
    public function testDrop()
    {
        $xs = Ginq::from([1,2,3,4,5,6,7,8,9])->drop(5)->toArray();
        $this->assertEquals([6,7,8,9], $xs);
    }

    /**
     * @todo Implement testTakeWhile().
     */
    public function testTakeWhile()
    {
        $xs = Ginq::from([1,2,3,4,5,6,7,8,9,8,7,6,5,4,3,2,1])
            ->takeWhile(function($x) { return $x <= 5; })
            ->toArray();
        $this->assertEquals([1,2,3,4,5], $xs);
    }

    /**
     * @todo Implement testDropWhile().
     */
    public function testDropWhile()
    {
        $xs = Ginq::from([1,2,3,4,5,6,7,8,9,8,7,6,5,4,3,2,1])
            ->dropWhile(function($x) { return $x <= 5; })
            ->toArray();
        $this->assertEquals([6,7,8,9,8,7,6,5,4,3,2,1], $xs);
    }

     /**
     * @todo Implement testConcat().
     */
    public function testConcat()
    {
        $xs = Ginq::from([1,2,3,4,5])->concat([6,7,8,9])->toArray();
        $this->assertEquals([1,2,3,4,5,6,7,8,9], $xs);
    }

    /**
     * testSelectMany().
     */
    public function testSelectMany()
    {
        $phoneBook =
            [['name'   => 'Taro'
             ,'phones' =>
                     ['03-1234-5678'
                     ,'090-8421-9061'
                     ]
             ]
            ,['name'   => 'Atsushi'
             ,'phones' =>
                     ['050-1198-4458'
                     ]
             ]
            ,['name'  => 'Junko'
            ,'phones' =>
                    ['06-1111-3333'
                    ,'090-9898-1314'
                    ,'050-6667-2231'
                    ]
             ]
            ];

        // without join selector
        $phones = Ginq::from($phoneBook)->selectMany('phones')->toArray();
        $this->assertEquals(
            ['03-1234-5678'
            ,'090-8421-9061'
            ,'050-1198-4458'
            ,'06-1111-3333'
            ,'090-9898-1314'
            ,'050-6667-2231'
            ], $phones);

        // with join selector
        $phones = Ginq::from($phoneBook)
            ->selectMany(
                'phones',
                function($person, $phone) {
                    return "${person['name']} : $phone";
                }
            )->toArray();
        $this->assertEquals(
            ['Taro : 03-1234-5678'
            ,'Taro : 090-8421-9061'
            ,'Atsushi : 050-1198-4458'
            ,'Junko : 06-1111-3333'
            ,'Junko : 090-9898-1314'
            ,'Junko : 050-6667-2231'
            ], $phones);
    }

    /**
     * @todo Implement testJoin().
     */
    public function testJoin()
    {
        // Remove the following line when you implement this test.
        throw new PHPUnit_Framework_IncompleteTestError;
    }

    /**
     * @todo Implement testZip().
     */
    public function testZip()
    {
        // Remove the following line when you implement this test.
        throw new PHPUnit_Framework_IncompleteTestError;
    }
 }

// Call GinqTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "GinqTest::main") {
    GinqTest::main();
}
?>
