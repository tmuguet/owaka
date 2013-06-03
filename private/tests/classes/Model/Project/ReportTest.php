<?php
defined('SYSPATH') or die('No direct access allowed!');

class Model_Project_ReportTest extends TestCase
{

    protected $xmlDataSet = 'main';

    /**
     * @covers Model_Project_Report::search
     */
    public function testSearch()
    {
        $actual1 = ORM::factory('Project_Report')
                ->search($this->genNumbers['ProjectFoo'], 'foo');
        $actual2 = ORM::factory('Project_Report')
                ->search($this->genNumbers['ProjectFoo'], 'baz');
        $actual3 = ORM::factory('Project_Report')
                ->search($this->genNumbers['ProjectBar'], 'foo');

        $this->assertNotNull($actual1, "Model not loaded");
        $this->assertEquals('bar', $actual1, "Incorrect return value");
        $this->assertNull($actual2, "Model should not be loaded");
        $this->assertNull($actual3, "Model should not be loaded");
    }
}