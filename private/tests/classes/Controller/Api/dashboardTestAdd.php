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
        $post     = array(
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
        
        $response = Request::factory('api/dashboard/add/main/Log')
                ->post($post)
                ->execute();
        $this->assertEquals(200, $response->status());
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `widgets` WHERE `id`=" . $actual['id']
        );
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
        $post     = array(
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
                ->post($post)
                ->execute();
        $this->assertEquals(200, $response->status());
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        unset($post['params']['bat']);
        unset($post['params']['project']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['id']
        );
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
        $post     = array(
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
                ->post($post)
                ->execute();
        $this->assertEquals(200, $response->status());
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `project_widgets` WHERE `id`=" . $actual['id']
        );
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
        $post     = array(
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
                ->post($post)
                ->execute();
        $this->assertEquals(200, $response->status());
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        unset($post['params']['bat']);
        unset($post['params']['project']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['id']
        );
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
        $post     = array(
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
                ->post($post)
                ->execute();
        $this->assertEquals(200, $response->status());
        
        $actual   = json_decode($response->body(), TRUE);
        $this->assertEquals('ok', $actual['res']);

        unset($post['params']['bat']);
        $result = Database::instance()->query(
                Database::SELECT, "SELECT * FROM `build_widgets` WHERE `id`=" . $actual['id']
        );
        $this->assertEquals($post['params'], json_decode($result->get('params'), TRUE));
        $this->assertEquals($post['width'], $result->get('width'));
        $this->assertEquals($post['height'], $result->get('height'));
        $this->assertEquals($post['column'], $result->get('column'));
        $this->assertEquals($post['row'], $result->get('row'));
    }
}