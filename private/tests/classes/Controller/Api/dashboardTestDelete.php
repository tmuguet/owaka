<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_dashboardTestDelete extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_dashboard::action_delete
     */
    public function testActionDeleteMain()
    {
        $expected = array('res' => 'ok');

        $response = Request::factory('api/dashboard/delete/main/' . $this->genNumbers['mainBackground'])->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['mainBackground']
                )->get('c')
        );
    }

    /**
     * @covers Controller_Api_dashboard::action_delete
     */
    public function testActionDeleteProject()
    {
        $expected = array('res' => 'ok');

        $response = Request::factory('api/dashboard/delete/project/' . $this->genNumbers['projectFooLog'])->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['projectFooLog']
                )->get('c')
        );
    }

    /**
     * @covers Controller_Api_dashboard::action_delete
     */
    public function testActionDeleteBuild()
    {
        $expected = array('res' => 'ok');

        $response = Request::factory('api/dashboard/delete/build/' . $this->genNumbers['buildFooBackground'])->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['buildFooBackground']
                )->get('c')
        );
    }
}