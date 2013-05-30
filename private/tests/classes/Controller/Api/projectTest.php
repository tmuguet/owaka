<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_projectTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_project::action_list
     */
    public function testActionList()
    {
        $expected = array(
            array('id'   => $this->genNumbers['ProjectBar'], 'name' => 'active-git'),
            array('id'   => $this->genNumbers['ProjectFoo'], 'name' => 'active-hg'),
        );
        $actual   = json_decode(
                Request::factory('api/project/list/')->execute()->body(), TRUE
        );
        $this->assertEquals($expected, $actual);
    }
}