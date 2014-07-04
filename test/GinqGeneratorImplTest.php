<?php

require_once "GinqTestBase.php";

/**
 * Test class for Ginq (Iterator impl).
 */
class GinqGeneratorImplTest extends GinqTestBase
{
    protected function setUp()
    {
        Ginq::useGenerator();
        parent::setUp();
    }
}

// Call GinqTest::main() if this source file is executed directly.
if (defined('PHPUnit_MAIN_METHOD') && PHPUnit_MAIN_METHOD == "GinqTest::main") {
    GinqGeneratorImplTest::main();
}

