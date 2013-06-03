<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_DesignerTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Designer::action_main
     * @covers Controller_Designer::render
     */
    public function testActionMain()
    {
        $response = Request::factory('designer/main')->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            ORM::factory('Widget', $this->genNumbers['mainBackground']),
            ORM::factory('Widget', $this->genNumbers['mainCoverage']),
        );
        $expected        = View::factory('designer')
                ->set('from', 'main')
                ->set('widgets', $expectedWidgets)
                ->set('controllers', array('widget1', 'widget2'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer::action_project
     * @covers Controller_Designer::render
     */
    public function testActionProject()
    {
        $response = Request::factory('designer/project/' . $this->genNumbers['ProjectFoo'])->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            ORM::factory('Project_Widget', $this->genNumbers['projectFooBackground']),
            ORM::factory('Project_Widget', $this->genNumbers['projectFooLog']),
        );
        $expected        = View::factory('designer')
                ->set('from', 'project')
                ->set('projectId', $this->genNumbers['ProjectFoo'])
                ->set('widgets', $expectedWidgets)
                ->set('controllers', array('widget1', 'widget3'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer::action_build
     * @covers Controller_Designer::render
     */
    public function testActionBuild()
    {
        $response = Request::factory('designer/build/' . $this->genNumbers['ProjectFoo'])->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            ORM::factory('Build_Widget', $this->genNumbers['buildFooBackground']),
            ORM::factory('Build_Widget', $this->genNumbers['buildFooLog']),
        );
        $expected        = View::factory('designer')
                ->set('from', 'build')
                ->set('projectId', $this->genNumbers['ProjectFoo'])
                ->set('widgets', $expectedWidgets)
                ->set('controllers', array('widget1', 'widget4'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}