<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_WidgetTestDelete extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Widget::action_delete
     */
    public function testActionDeleteMain()
    {
        $response = Request::factory('api/widget/delete/main/' . $this->genNumbers['mainBackground'])->login()->execute();
        $this->assertResponseOK($response);

        $this->rollback();
        $this->assertEquals(
                array('widget' => $this->genNumbers['mainBackground']), json_decode($response->body(), TRUE),
                                                                                    "Incorrect API result"
        );

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['mainBackground']
                )->get('c'), "Deletion not effective"
        );
    }

    /**
     * @covers Controller_Api_Widget::action_delete
     */
    public function testActionDeleteProject()
    {
        $response = Request::factory('api/widget/delete/project/' . $this->genNumbers['projectFooLog'])->login()->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array('widget' => $this->genNumbers['projectFooLog']), json_decode($response->body(), TRUE),
                                                                                   "Incorrect API result"
        );

        $this->rollback();

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['projectFooLog']
                )->get('c'), "Deletion not effective"
        );
    }

    /**
     * @covers Controller_Api_Widget::action_delete
     */
    public function testActionDeleteBuild()
    {
        $response = Request::factory('api/widget/delete/build/' . $this->genNumbers['buildFooBackground'])->login()->execute();
        $this->assertResponseOK($response);
        $this->assertEquals(
                array('widget' => $this->genNumbers['buildFooBackground']), json_decode($response->body(), TRUE),
                                                                                        "Incorrect API result"
        );

        $this->rollback();

        $this->assertEquals(
                0,
                Database::instance()->query(
                        Database::SELECT,
                        "SELECT COUNT(*) AS c FROM `widgets` WHERE `id`=" . $this->genNumbers['buildFooBackground']
                )->get('c'), "Deletion not effective"
        );
    }

    /**
     * @covers Controller_Api_Widget::action_delete
     */
    public function testActionDeleteNotFound()
    {
        $response = Request::factory('api/widget/delete/main/99999')->login()->execute();
        $this->assertResponseStatusEquals(Response::NOTFOUND, $response);
    }
}