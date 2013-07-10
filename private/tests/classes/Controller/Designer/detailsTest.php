<?php
defined('SYSPATH') or die('No direct access allowed!');

class Controller_Designer_detailsTest extends TestCase
{

    protected $useDatabase = FALSE;

    /**
     * @covers Controller_Designer_details::action_main
     * @covers Controller_Designer_details::process
     */
    public function testActionMain()
    {
        $response = Request::factory('designer_details/main/Log')
                ->login()
                ->post('projectId', 1)
                ->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $params                       = Controller_Widget_Log::getExpectedParameters('main');
        $params['project']['default'] = 1;
        $expected                     = View::factory('designer_widgetdetails')
                ->set('from', 'main')
                ->set('widget', 'Log')
                ->set('size', Controller_Widget_Log::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Log::getOptimizedSizes())
                ->set('params', $params);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer_details::action_main
     * @covers Controller_Designer_details::process
     */
    public function testActionMain2()
    {
        $response = Request::factory('designer_details/main/Background')
                ->login()
                ->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expected = View::factory('designer_widgetdetails')
                ->set('from', 'main')
                ->set('widget', 'Background')
                ->set('size', Controller_Widget_Background::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Background::getOptimizedSizes())
                ->set('params', Controller_Widget_Background::getExpectedParameters('main'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer_details::action_project
     * @covers Controller_Designer_details::process
     */
    public function testActionProject()
    {
        $response = Request::factory('designer_details/project/Log')
                ->login()
                ->post('projectId', 1)
                ->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $params                       = Controller_Widget_Log::getExpectedParameters('project');
        $params['project']['default'] = 1;
        $expected                     = View::factory('designer_widgetdetails')
                ->set('from', 'project')
                ->set('widget', 'Log')
                ->set('size', Controller_Widget_Log::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Log::getOptimizedSizes())
                ->set('params', $params);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer_details::action_project
     * @covers Controller_Designer_details::process
     */
    public function testActionProject2()
    {
        $response = Request::factory('designer_details/project/Background')
                ->login()
                ->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expected = View::factory('designer_widgetdetails')
                ->set('from', 'project')
                ->set('widget', 'Background')
                ->set('size', Controller_Widget_Background::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Background::getOptimizedSizes())
                ->set('params', Controller_Widget_Background::getExpectedParameters('project'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer_details::action_build
     * @covers Controller_Designer_details::process
     */
    public function testActionBuild()
    {
        $response = Request::factory('designer_details/build/Log')
                ->login()
                ->post('projectId', 1)
                ->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $params                       = Controller_Widget_Log::getExpectedParameters('build');
        $params['project']['default'] = 1;
        $expected                     = View::factory('designer_widgetdetails')
                ->set('from', 'build')
                ->set('widget', 'Log')
                ->set('size', Controller_Widget_Log::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Log::getOptimizedSizes())
                ->set('params', $params);
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }

    /**
     * @covers Controller_Designer_details::action_build
     * @covers Controller_Designer_details::process
     */
    public function testActionBuild2()
    {
        $response = Request::factory('designer_details/build/Background')->login()->execute();
        $this->assertEquals(200, $response->status(), "Request failed");

        $expected = View::factory('designer_widgetdetails')
                ->set('from', 'build')
                ->set('widget', 'Background')
                ->set('size', Controller_Widget_Background::getPreferredSize())
                ->set('availableSizes', Controller_Widget_Background::getOptimizedSizes())
                ->set('params', Controller_Widget_Background::getExpectedParameters('build'));
        $this->assertEquals($expected->render(), $response->body(), "Rendering incorrect");
    }
}