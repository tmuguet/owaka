<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_buildTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_build::action_list
     */
    public function testActionList()
    {
        $expected = array(
            array('id'       => $this->genNumbers['build5'], 'revision' => 'r44', 'status'   => 'queued'),
            array('id'       => $this->genNumbers['build4'], 'revision' => 'r43', 'status'   => 'building'),
            array('id'       => $this->genNumbers['build3'], 'revision' => 'r42', 'status'   => 'error'),
            array('id'       => $this->genNumbers['build2'], 'revision' => 'r41', 'status'   => 'unstable'),
            array('id'       => $this->genNumbers['build1'], 'revision' => 'r40', 'status'   => 'ok'),
        );
        
        $response = Request::factory('api/build/list/' . $this->genNumbers['ProjectFoo'])->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_build::action_list
     */
    public function testActionListEmpty()
    {
        $expected = array();
        
        $response = Request::factory('api/build/list/' . $this->genNumbers['ProjectBar'])->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_build::action_list
     */
    public function testActionListInactive()
    {
        $expected = array(
            array('id'       => $this->genNumbers['buildBat1'], 'revision' => 'abcdefghij', 'status'   => 'ok'),
        );
        
        $response = Request::factory('api/build/list/' . $this->genNumbers['ProjectBat'])->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }
}