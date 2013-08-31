<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Phpunit_ErrorTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers Model_Phpunit_Error::hasSimilar
     */
    public function testHasSimilar()
    {
        $target            = new Model_Phpunit_Error();
        $target->testsuite = 'foobar';
        $target->testcase  = 'case';

        $target2            = new Model_Phpunit_Error();
        $target2->testsuite = 'foobar';
        $target2->testcase  = 'case2';

        $build = ORM::factory('Build', $this->genNumbers['build1']);

        $this->assertTrue($target->hasSimilar($build));
        $this->assertFalse($target2->hasSimilar($build));
    }
}