<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_DashboardTest extends TestCase
{

    protected $xmlDataSet = 'data';

    /**
     * @covers Controller_Dashboard::action_main
     */
    public function testActionMain()
    {
        $response = Request::factory('dashboard/')->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            Request::factory('w/main/Background/display/' . $this->genNumbers['mainBackground'])->execute(),
            Request::factory('w/main/coverage_BuildIcon/display/' . $this->genNumbers['mainCoverage'])->execute(),
        );
        $expected        = View::factory('dashboard')
                ->set('from', 'main')
                ->set('widgets', $expectedWidgets);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Dashboard::action_project
     */
    public function testActionProject()
    {
        $response = Request::factory('dashboard/project/' . $this->genNumbers['ProjectFoo'])->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            Request::factory(
                    'w/project/Background/display/' . $this->genNumbers['projectFooBackground']
                    . '/' . $this->genNumbers['ProjectFoo']
            )->execute(),
            Request::factory(
                    'w/project/Log/display/' . $this->genNumbers['projectFooLog']
                    . '/' . $this->genNumbers['ProjectFoo']
            )->execute(),
        );
        $expected        = View::factory('dashboard')
                ->set('from', 'project')
                ->set('projectId', $this->genNumbers['ProjectFoo'])
                ->set('widgets', $expectedWidgets);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Dashboard::action_build
     */
    public function testActionBuild()
    {
        $response = Request::factory('dashboard/build/' . $this->genNumbers['build1'])->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expectedWidgets = array(
            Request::factory(
                    'w/build/Background/display/' . $this->genNumbers['buildFooBackground']
                    . '/' . $this->genNumbers['build1']
            )->execute(),
            Request::factory(
                    'w/build/Log/display/' . $this->genNumbers['buildFooLog'] . '/' . $this->genNumbers['build1']
            )->execute(),
        );
        $expected        = View::factory('dashboard')
                ->set('from', 'build')
                ->set('projectId', $this->genNumbers['ProjectFoo'])
                ->set('buildId', $this->genNumbers['build1'])
                ->set('widgets', $expectedWidgets);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}