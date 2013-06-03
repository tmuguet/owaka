<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_ProjectTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers Model_Project::lastBuild
     */
    public function testLastBuild()
    {
        $target1 = ORM::factory('Project', $this->genNumbers['ProjectFoo']);
        $target2 = ORM::factory('Project', $this->genNumbers['ProjectBar']);

        $expected = ORM::factory('Build', $this->genNumbers['build5']);

        $this->assertTrue($target1->loaded(), "Target not loaded");
        $this->assertTrue($target2->loaded(), "Target not loaded");

        $actual1 = $target1->lastBuild()->find();
        $this->assertTrue($actual1->loaded(), "Last build not found");
        $this->assertEquals($expected, $actual1, "Wrong build loaded");

        $actual2 = $target2->lastBuild()->find();
        $this->assertFalse($actual2->loaded(), "Last build found");
    }
}