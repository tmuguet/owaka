<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_BuildTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Build::action_list
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
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_list
     */
    public function testActionListEmpty()
    {
        $expected = array();

        $response = Request::factory('api/build/list/' . $this->genNumbers['ProjectBar'])->login()->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_list
     */
    public function testActionListInactive()
    {
        $expected = array(
            array('id'       => $this->genNumbers['buildBat1'], 'revision' => 'abcdefghij', 'status'   => 'ok'),
        );

        $response = Request::factory('api/build/list/' . $this->genNumbers['ProjectBat'])->login()->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_delete
     */
    public function testActionDeleteFirst()
    {
        $expected = array('build' => $this->genNumbers['build1'], 'next_build' => $this->genNumbers['build2']);

        $response = Request::factory('api/build/delete/' . $this->genNumbers['build1'])->login()->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_delete
     */
    public function testActionDeleteLast()
    {
        $expected = array('build' => $this->genNumbers['build5'], 'next_build' => $this->genNumbers['build4']);

        $response = Request::factory('api/build/delete/' . $this->genNumbers['build5'])->login()->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_delete
     */
    public function testActionDeleteOnly()
    {
        $expected = array('build' => $this->genNumbers['buildBat1'], 'next_build' => '');

        $response = Request::factory('api/build/delete/' . $this->genNumbers['buildBat1'])->login()->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual, "Incorrect API result");
    }

    /**
     * @covers Controller_Api_Build::action_delete
     */
    public function testActionDeleteMissing()
    {
        $response = Request::factory('api/build/delete/99999')->login()->execute();
        $this->assertResponseStatusEquals(404, $response);
    }
}