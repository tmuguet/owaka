<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller
{

    public function action_index()
    {
        $widgets     = ORM::factory('Widget')->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = View::factory('widgets/' . $widget->type)
                    ->set('widget', $widget);
        }

        $view = View::factory('dashboard')
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }

    public function action_build()
    {
        $buildId     = $this->request->param('id');
        $widgets     = ORM::factory('Build_Widget')->find_all();
        $widgetsView = array();
        foreach ($widgets as $widget) {
            $widgetsView[] = View::factory('widgets/' . $widget->type)
                    ->set('widget', $widget)
                    ->set('buildId', $buildId);
        }

        $view = View::factory('dashboard')
                ->set('widgets', $widgetsView);

        $this->response->body($view);
    }
}

// End Welcome
