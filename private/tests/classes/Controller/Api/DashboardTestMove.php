<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_DashboardTestMove extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Dashboard::action_move
     */
    public function testActionMoveMain()
    {
        $response = Request::factory('api/dashboard/move/main/' . $this->genNumbers['mainBackground'])
                ->login()
                ->post('column', '1')
                ->post('row', '42')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array('widget' => $this->genNumbers['mainBackground']), json_decode($response->body(), TRUE),
                                                                                    "Incorrect API result"
        );

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `widgets` WHERE `id`=" . $this->genNumbers['mainBackground']
        );
        $this->assertEquals(1, $result->get('column'), "Widget.column incorrect");
        $this->assertEquals(42, $result->get('row'), "Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_move
     */
    public function testActionMoveProject()
    {
        $response = Request::factory('api/dashboard/move/project/' . $this->genNumbers['projectBarLog'])
                ->login()
                ->post('column', '42')
                ->post('row', '5')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array('widget' => $this->genNumbers['projectBarLog']), json_decode($response->body(), TRUE),
                                                                                   "Incorrect API result"
        );

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `project_widgets` WHERE `id`=" . $this->genNumbers['projectBarLog']
        );
        $this->assertEquals(42, $result->get('column'), "Project_Widget.column incorrect");
        $this->assertEquals(5, $result->get('row'), "Project°Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_move
     */
    public function testActionMoveBuild()
    {
        $response = Request::factory('api/dashboard/move/build/' . $this->genNumbers['buildFooBackground'])
                ->login()
                ->post('column', '10')
                ->post('row', '11')
                ->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array('widget' => $this->genNumbers['buildFooBackground']), json_decode($response->body(), TRUE),
                                                                                        "Incorrect API result"
        );

        $result = Database::instance()->query(
                Database::SELECT,
                "SELECT `column`,`row` FROM `build_widgets` WHERE `id`=" . $this->genNumbers['buildFooBackground']
        );
        $this->assertEquals(10, $result->get('column'), "Build_Widget.column incorrect");
        $this->assertEquals(11, $result->get('row'), "Build_Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_move
     */
    public function testActionMoveFailed()
    {
        $response = Request::factory('api/dashboard/move/main/' . $this->genNumbers['mainBackground'])
                ->login()
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
    }

    /**
     * @covers Controller_Api_Dashboard::action_move
     */
    public function testActionMoveNotFound()
    {
        $response = Request::factory('api/dashboard/move/main/99999')->login()->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }
}