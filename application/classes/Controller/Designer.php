<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Displays dashboard designers
 * 
 * @package   Designer
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Designer extends Controller
{

    /**
     * Designer for main dashboard
     * 
     * @url http://example.com/designer/main
     */
    public function action_main()
    {
        $widgets = ORM::factory('Widget')
                ->find_all();
        $this->render($widgets);
    }

    /**
     * Designer for project dashboard
     * 
     * @url http://example.com/designer/project/&lt;project_id&gt;
     */
    public function action_project()
    {
        $projectId = $this->request->param('id');
        $widgets   = ORM::factory('Project_Widget')->where('project_id', '=', $projectId)->find_all();
        $this->render($widgets, $projectId);
    }

    /**
     * Designer for build dashboard
     * 
     * @url http://example.com/designer/build/&lt;project_id&gt;
     */
    public function action_build()
    {
        $projectId = $this->request->param('id');
        $widgets   = ORM::factory('Build_Widget')->where('project_id', '=', $projectId)->find_all();
        $this->render($widgets, $projectId);
    }

    /**
     * Renders designer
     * 
     * @param (Model_Widget|Model_Project_Widget|Model_Build_Widget)[] $widgets   List of registered widgets
     * @param int|null                                                 $projectId Project ID
     */
    protected function render($widgets, $projectId = NULL)
    {
        $controllers = array();
        foreach (File::findWidgets($this->request->action()) as $controller) {
            $controllers[]  = str_replace('Controller_Widget_', '', $controller);
        }

        $view = View::factory('designer')
                ->set('from', $this->request->action())
                ->set('widgets', $widgets)
                ->set('controllers', $controllers);
        if ($projectId !== NULL) {
            $view->set('projectId', $projectId);
        }

        $this->success($view);
    }
}
