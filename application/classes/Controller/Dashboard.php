<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Displays dashboards
 * 
 * @package Main
 */
class Controller_Dashboard extends Controller
{

    /**
     * Displays main dashboard
     * 
     * @url http://example.com/dashboard/main
     */
    public function action_main()
    {       
        $widgets     = ORM::factory('Widget')
                ->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/main/' . $widget->type . '/display/' . $widget->id)->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'main')
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }

    /**
     * Displays project dashboard
     * 
     * @url http://example.com/dashboard/project/&lt;project_id&gt;
     */
    public function action_project()
    {
        $projectId   = $this->request->param('id');
        $widgets     = ORM::factory('Project_Widget')
                ->where('project_id', '=', $projectId)
                ->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/project/' . $widget->type . '/display/' . $widget->id . '/' . $projectId)
                    ->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'project')
                ->set('projectId', $projectId)
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }

    /**
     * Displays build dashboard
     * 
     * @url http://example.com/dashboard/build/&lt;build_id&gt;
     */
    public function action_build()
    {
        $buildId     = $this->request->param('id');
        $build       = ORM::factory('Build', $buildId);
        $widgets     = ORM::factory('Build_Widget')
                ->where('project_id', '=', $build->project_id)
                ->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/build/' . $widget->type . '/display/' . $widget->id . '/' . $buildId)
                    ->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'build')
                ->set('projectId', $build->project_id)
                ->set('buildId', $buildId)
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }
}
