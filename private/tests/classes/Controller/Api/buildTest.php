<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_buildTest extends TestCase
{

    protected $xmlDataSet = 'data';

    public function testActionList()
    {
        $expected = array(
            array('id'       => $this->genNumbers['build5'], 'revision' => 'r44', 'status'   => 'queued'),
            array('id'       => $this->genNumbers['build4'], 'revision' => 'r43', 'status'   => 'building'),
            array('id'       => $this->genNumbers['build3'], 'revision' => 'r42', 'status'   => 'error'),
            array('id'       => $this->genNumbers['build2'], 'revision' => 'r41', 'status'   => 'unstable'),
            array('id'       => $this->genNumbers['build1'], 'revision' => 'r40', 'status'   => 'ok'),
        );
        $actual   = json_decode(
                Request::factory('api/build/list/' . $this->genNumbers['ProjectFoo'])->execute()->body(), TRUE
        );
        $this->assertEquals($expected, $actual);
    }

    public function testActionListEmpty()
    {
        $expected = array();
        $actual   = json_decode(
                Request::factory('api/build/list/' . $this->genNumbers['ProjectBar'])->execute()->body(), TRUE
        );
        $this->assertEquals($expected, $actual);
    }

    public function testActionListInactive()
    {
        $expected = array(
            array('id'       => $this->genNumbers['buildBat1'], 'revision' => 'abcdefghij', 'status'   => 'ok'),
        );
        $actual   = json_decode(
                Request::factory('api/build/list/' . $this->genNumbers['ProjectBat'])->execute()->body(), TRUE
        );
        $this->assertEquals($expected, $actual);
    }
}