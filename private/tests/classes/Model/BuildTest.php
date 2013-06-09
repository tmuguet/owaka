<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_BuildTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers ORM::exists
     */
    public function testExists()
    {
        $this->assertTrue(Model_Build::exists($this->genNumbers['build1']), "Nominal test");
        $this->assertFalse(Model_Build::exists($this->genNumbers['ProjectFoo']), "Wrong ID");
        $this->assertFalse(Model_Build::exists(0), "ID=0");
        $this->assertFalse(Model_Build::exists(-1), "ID=-1");
        $this->assertFalse(Model_Build::exists('foo'), "ID=foo");
    }

    /**
     * @covers Model_Build::previousBuild
     */
    public function testPreviousBuild()
    {
        $target1 = ORM::factory('Build', $this->genNumbers['build1']);
        $target2 = ORM::factory('Build', $this->genNumbers['build2']);

        $this->assertTrue($target1->loaded(), "Target not loaded");
        $this->assertTrue($target2->loaded(), "Target not loaded");

        $actual1 = $target1->previousBuild()->find();
        $this->assertFalse($actual1->loaded(), "Previous build found");

        $actual2 = $target2->previousBuild()->find();
        $this->assertTrue($actual2->loaded(), "Previous build not found");
        $this->assertEquals($target1, $actual2, "Wrong build loaded");
    }

    /**
     * @covers Model_Build::nextBuild
     */
    public function testNextBuild()
    {
        $target1 = ORM::factory('Build', $this->genNumbers['build5']);
        $target2 = ORM::factory('Build', $this->genNumbers['build4']);

        $this->assertTrue($target1->loaded(), "Target not loaded");
        $this->assertTrue($target2->loaded(), "Target not loaded");

        $actual1 = $target1->nextBuild()->find();
        $this->assertFalse($actual1->loaded(), "Next build found");

        $actual2 = $target2->nextBuild()->find();
        $this->assertTrue($actual2->loaded(), "Next build not found");
        $this->assertEquals($target1, $actual2, "Wrong build loaded");
    }

    /**
     * @covers Model_Build::rangeBuild
     */
    public function testRangeBuild()
    {
        $target   = ORM::factory('Build', $this->genNumbers['build4']);
        $expected = array(
            ORM::factory('Build', $this->genNumbers['build5']),
            ORM::factory('Build', $this->genNumbers['build4']),
            ORM::factory('Build', $this->genNumbers['build3']),
            ORM::factory('Build', $this->genNumbers['build2']),
            ORM::factory('Build', $this->genNumbers['build1']),
        );

        $this->assertTrue($target->loaded(), "Target not loaded");

        $actual = $target->rangeBuild();
        $this->assertEquals($expected, $actual, "Wrong builds loaded");
    }

    /**
     * @covers Model_Build::getRevision
     */
    public function testGetRevision()
    {
        $target1 = ORM::factory('Build', $this->genNumbers['build4']);
        $target2 = ORM::factory('Build', $this->genNumbers['buildBat1']);
        $this->assertTrue($target1->loaded(), "Target not loaded");
        $this->assertTrue($target2->loaded(), "Target not loaded");

        $this->assertEquals('r43', $target1->getRevision(), "Wrong revision for numeric types");
        $this->assertEquals('abcdefghij', $target2->getRevision(), "Wrong revision for non-numeric types");
    }
}