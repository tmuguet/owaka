<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_dashboardTestMove extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_dashboard::action_move
     */
    public function testActionMoveMain()
    {
        $expected = array('res' => 'ok', 'id'  => $this->genNumbers['mainBackground']);

        $response = Request::factory('api/dashboard/move/main/' . $this->genNumbers['mainBackground'])
                ->post('column', '1')
                ->post('row', '42')
                ->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `widgets` WHERE `id`=" . $this->genNumbers['mainBackground']
        );
        $this->assertEquals(1, $result->get('column'));
        $this->assertEquals(42, $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_move
     */
    public function testActionMoveProject()
    {
        $expected = array('res' => 'ok', 'id'  => $this->genNumbers['projectBarLog']);

        $response = Request::factory('api/dashboard/move/project/' . $this->genNumbers['projectBarLog'])
                ->post('column', '42')
                ->post('row', '5')
                ->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `project_widgets` WHERE `id`=" . $this->genNumbers['projectBarLog']
        );
        $this->assertEquals(42, $result->get('column'));
        $this->assertEquals(5, $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_move
     */
    public function testActionMoveBuild()
    {
        $expected = array('res' => 'ok', 'id'  => $this->genNumbers['buildFooBackground']);

        $response = Request::factory('api/dashboard/move/build/' . $this->genNumbers['buildFooBackground'])
                ->post('column', '10')
                ->post('row', '11')
                ->execute();
        $this->assertEquals(200, $response->status());

        $actual = json_decode($response->body(), TRUE);
        $this->assertEquals($expected, $actual);

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `build_widgets` WHERE `id`=" . $this->genNumbers['buildFooBackground']
        );
        $this->assertEquals(10, $result->get('column'));
        $this->assertEquals(11, $result->get('row'));
    }
}