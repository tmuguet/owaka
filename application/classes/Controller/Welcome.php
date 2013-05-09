<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller
{

    public function action_main()
    {
        $widgets     = ORM::factory('Widget')
                ->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/' . $widget->type . '/main/' . $widget->id)->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'main')
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }

    public function action_project()
    {
        $projectId   = $this->request->param('id');
        $widgets     = ORM::factory('Project_Widget')->where('project_id', '=', $projectId)->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/' . $widget->type . '/project/' . $widget->id)->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'project')
                ->set('projectId', $projectId)
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }

    public function action_build()
    {
        $buildId     = $this->request->param('id');
        $build = ORM::factory('Build', $buildId);
        
        $widgets     = ORM::factory('Build_Widget')->where('project_id', '=', $build->project_id)->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = Request::factory('w/' . $widget->type . '/build/' . $widget->id)->execute();
        }

        $view = View::factory('dashboard')
                ->set('from', 'build')
                ->set('projectId', $build->project_id)
                ->set('buildId', $buildId)
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }
}

// End Welcome
