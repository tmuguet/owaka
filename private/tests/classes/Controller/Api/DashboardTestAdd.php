<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_DashboardTestAdd extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddMain()
    {
        $post = array(
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );

        $response = Request::factory('api/dashboard/add/main/Log')
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertArrayHasKey('widget', $actual, "Incorrect API result");
        $this->assertNotEmpty($actual['widget'], "Incorrect API result");

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `widgets` WHERE `id`=" . $actual['widget']
        );
        $this->assertEquals(array(), json_decode($result->get('params'), TRUE), "Widget.params incorrect");
        $this->assertEquals($post['width'], $result->get('width'), "Widget.width incorrect");
        $this->assertEquals($post['height'], $result->get('height'), "Widget.height incorrect");
        $this->assertEquals($post['column'], $result->get('column'), "Widget.column incorrect");
        $this->assertEquals($post['row'], $result->get('row'), "Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddProject1()
    {
        $post = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat"     => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );

        $response = Request::factory('api/dashboard/add/project/Log/' . $this->genNumbers['ProjectFoo'])
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertArrayHasKey('widget', $actual, "Incorrect API result");
        $this->assertNotEmpty($actual['widget'], "Incorrect API result");

        unset($post['params']['bat']);
        unset($post['params']['project']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['widget']
        );
        $this->assertEquals(
                $post['params'], json_decode($result->get('params'), TRUE), "Project_Widget.params incorrect"
        );
        $this->assertEquals($post['width'], $result->get('width'), "Project_Widget.width incorrect");
        $this->assertEquals($post['height'], $result->get('height'), "Project_Widget.height incorrect");
        $this->assertEquals($post['column'], $result->get('column'), "Project_Widget.column incorrect");
        $this->assertEquals($post['row'], $result->get('row'), "Project_Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddProject2()
    {
        $post = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat"     => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );

        $response = Request::factory('api/dashboard/add/project/Log/' . $this->genNumbers['ProjectBar'])
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertArrayHasKey('widget', $actual, "Incorrect API result");
        $this->assertNotEmpty($actual['widget'], "Incorrect API result");

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['widget']
        );
        $this->assertEquals(
                $post['params'], json_decode($result->get('params'), TRUE), "Project_Widget.params incorrect"
        );
        $this->assertEquals($post['width'], $result->get('width'), "Project_Widget.width incorrect");
        $this->assertEquals($post['height'], $result->get('height'), "Project_Widget.height incorrect");
        $this->assertEquals($post['column'], $result->get('column'), "Project_Widget.column incorrect");
        $this->assertEquals($post['row'], $result->get('row'), "Project_Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddBuild1()
    {
        $post = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat"     => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );

        $response = Request::factory('api/dashboard/add/build/Log/' . $this->genNumbers['ProjectFoo'])
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertArrayHasKey('widget', $actual, "Incorrect API result");
        $this->assertNotEmpty($actual['widget'], "Incorrect API result");

        unset($post['params']['bat']);
        unset($post['params']['project']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['widget']
        );
        $this->assertEquals(
                $post['params'], json_decode($result->get('params'), TRUE), "Build_Widget.params incorrect"
        );
        $this->assertEquals($post['width'], $result->get('width'), "Build_Widget.width incorrect");
        $this->assertEquals($post['height'], $result->get('height'), "Build_Widget.height incorrect");
        $this->assertEquals($post['column'], $result->get('column'), "Build_Widget.column incorrect");
        $this->assertEquals($post['row'], $result->get('row'), "Build_Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddBuild2()
    {
        $post = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat"     => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );

        $response = Request::factory('api/dashboard/add/build/Log/' . $this->genNumbers['ProjectBar'])
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseOK($response);

        $actual = json_decode($response->body(), TRUE);
        $this->assertArrayHasKey('widget', $actual, "Incorrect API result");
        $this->assertNotEmpty($actual['widget'], "Incorrect API result");

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['widget']
        );
        $this->assertEquals(
                $post['params'], json_decode($result->get('params'), TRUE), "Build_Widget.params incorrect"
        );
        $this->assertEquals($post['width'], $result->get('width'), "Build_Widget.width incorrect");
        $this->assertEquals($post['height'], $result->get('height'), "Build_Widget.height incorrect");
        $this->assertEquals($post['column'], $result->get('column'), "Build_Widget.column incorrect");
        $this->assertEquals($post['row'], $result->get('row'), "Build_Widget.row incorrect");
    }

    /**
     * @covers Controller_Api_Dashboard::action_add
     */
    public function testActionAddFailed()
    {
        $post = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat"     => ""
            )
        );

        $response = Request::factory('api/dashboard/add/build/Log' . $this->genNumbers['ProjectBar'])
                ->login()
                ->post($post)
                ->execute();
        $this->assertResponseStatusEquals(Response::UNPROCESSABLE, $response);
        $this->assertEquals(
                array(
            'errors' => array(
                'width'  => 'You must provide a width.',
                'height' => 'You must provide a height.',
                'column' => 'You must provide a column.',
                'row'    => 'You must provide a row.'
            )
                ), json_decode($response->body(), TRUE), "Incorrect API result"
        );
    }
}