<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Designer extends Controller
{

    private function _getWidgets($path)
    {
        $phpFiles = glob($path . '*.php');

        $dirs = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $phpFiles = array_merge($phpFiles, $this->_getWidgets($dir));
        }
        return $phpFiles;
    }

    protected function getWidgets($from)
    {
        $files   = $this->_getWidgets(APPPATH . 'classes/Controller/Widget/');
        $widgets = array();
        foreach ($files as $file) {
            $nameWidget = str_replace('/', '_',
                                      str_replace(APPPATH . 'classes/Controller/Widget/', '', substr($file, 0, -4)));
            if (!class_exists('Controller_Widget_' . $nameWidget, FALSE)) {
                include_once $file;
            }

            $class = new ReflectionClass('Controller_Widget_' . $nameWidget);
            if ($class->isInstantiable() && $class->hasMethod('action_' . $from)) {
                $widgets[] = $nameWidget;
            }
        }
        return $widgets;
    }

    public function action_main()
    {
        $widgets = ORM::factory('Widget')
                ->find_all();
        $this->show($widgets);
    }

    public function action_project()
    {
        $projectId = $this->request->param('id');
        $widgets   = ORM::factory('Project_Widget')->where('project_id', '=', $projectId)->find_all();
        $this->show($widgets, $projectId);
    }

    public function action_build()
    {
        $buildId = $this->request->param('id');
        $build   = ORM::factory('Build', $buildId);
        $widgets = ORM::factory('Build_Widget')->where('project_id', '=', $build->project_id)->find_all();
        $this->show($widgets, $build->project_id, $buildId);
    }

    protected function show($widgets, $projectId = NULL, $buildId = NULL)
    {

        $controllers = array();
        foreach ($this->getWidgets($this->request->action()) as $controller) {
            $name           = "Controller_Widget_" . $controller;
            $size           = $name::getPreferredSize();
            $availableSizes = $name::getOptimizedSizes();
            $controllers[]  = array(
                "widget"         => $controller,
                "size"           => $size,
                "availableSizes" => $availableSizes
            );
        }

        $view = View::factory('designer')
                ->set('from', $this->request->action())
                ->set('widgets', $widgets)
                ->set('controllers', $controllers);
        if ($projectId !== NULL) {
            $view->set('projectId', $projectId);
        }
        if ($buildId !== NULL) {
            $view->set('buildId', $buildId);
        }

        $this->response->body($view);
    }
}
