<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Displays dashboard designers
 */
class Controller_Designer extends Controller
{

    /**
     * Finds all widgets in application
     * @param string $path Absolute path where to search widgets
     * @return string[] List of absolute paths to PHP files of widgets, unfiltered
     */
    private function _getWidgets($path)
    {
        $phpFiles = glob($path . '*.php');

        $dirs = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $phpFiles = array_merge($phpFiles, $this->_getWidgets($dir));
        }
        return $phpFiles;
    }

    /**
     * Finds all widgets
     * @param string $dashboard Type of dashboard
     * @return string[] Name of widgets
     */
    protected function getWidgets($dashboard)
    {
        $files   = $this->_getWidgets(APPPATH . 'classes/Controller/Widget/');
        $widgets = array();
        foreach ($files as $file) {
            $nameWidget = str_replace(
                    '/', '_', str_replace(APPPATH . 'classes/Controller/Widget/', '', substr($file, 0, -4))
            );
            if (!class_exists('Controller_Widget_' . $nameWidget, FALSE)) {
                include_once $file;
            }

            $class = new ReflectionClass('Controller_Widget_' . $nameWidget);
            if ($class->isInstantiable() && ($class->hasMethod('display_' . $dashboard) || $class->hasMethod('display_all'))) {
                $widgets[] = $nameWidget;
            }
        }
        return $widgets;
    }

    /**
     * Designer for main dashboard
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
     * @url http://example.com/designer/project/<project_id>
     */
    public function action_project()
    {
        $projectId = $this->request->param('id');
        $widgets   = ORM::factory('Project_Widget')->where('project_id', '=', $projectId)->find_all();
        $this->render($widgets, $projectId);
    }

    /**
     * Designer for build dashboard
     * @url http://example.com/designer/build/<project_id>
     */
    public function action_build()
    {
        $projectId = $this->request->param('id');
        $widgets = ORM::factory('Build_Widget')->where('project_id', '=', $projectId)->find_all();
        $this->render($widgets, $projectId);
    }

    /**
     * Renders designer
     * @param (Model_Widget|Model_Project_Widget|Model_Build_Widget)[] $widgets   List of registered widgets
     * @param int|null                                                 $projectId Project ID
     */
    protected function render($widgets, $projectId = NULL)
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

        $this->response->body($view);
    }
}
