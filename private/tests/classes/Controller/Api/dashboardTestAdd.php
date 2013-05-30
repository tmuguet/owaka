<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Api_dashboardTestAdd extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Api_dashboard::action_add
     */
    public function testActionAddMain()
    {
        $post    = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat" => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );
        $request = Request::factory('api/dashboard/add/main/Log')
                ->post($post);
        $actual  = json_decode($request->execute()->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `widgets` WHERE `id`=" . $actual['id']
        );
        unset($post['params']['bat']);
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_add
     */
    public function testActionAddProject1()
    {
        $post    = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat" => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );
        $request = Request::factory('api/dashboard/add/project/Log/' . $this->genNumbers['ProjectFoo'])
                ->post($post);
        $actual  = json_decode($request->execute()->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['id']
        );
        unset($post['params']['bat']);
        unset($post['params']['project']);
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_add
     */
    public function testActionAddProject2()
    {
        $post    = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat" => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );
        $request = Request::factory('api/dashboard/add/project/Log/' . $this->genNumbers['ProjectBar'])
                ->post($post);
        $actual  = json_decode($request->execute()->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['id']
        );
        unset($post['params']['bat']);
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_add
     */
    public function testActionAddBuild1()
    {
        $post    = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat" => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );
        $request = Request::factory('api/dashboard/add/build/Log/' . $this->genNumbers['ProjectFoo'])
                ->post($post);
        $actual  = json_decode($request->execute()->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['id']
        );
        unset($post['params']['bat']);
        unset($post['params']['project']);
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }

    /**
     * @covers Controller_Api_dashboard::action_add
     */
    public function testActionAddBuild2()
    {
        $post    = array(
            "params" => array(
                "foo"     => "bar",
                "project" => $this->genNumbers['ProjectFoo'],
                "bat" => ""
            ),
            "width"  => 4,
            "height" => 2,
            "column" => 1,
            "row"    => 42
        );
        $request = Request::factory('api/dashboard/add/build/Log/' . $this->genNumbers['ProjectBar'])
                ->post($post);
        $actual  = json_decode($request->execute()->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['id']
        );
        unset($post['params']['bat']);
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }
}