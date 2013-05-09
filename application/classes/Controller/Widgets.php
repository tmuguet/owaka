<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Widgets extends Controller
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

    protected function getWidgets()
    {
        $files   = $this->_getWidgets(APPPATH . 'classes/Controller/Widget/');
        $widgets = array();
        foreach ($files as $file) {
            $nameWidget = str_replace('/', '_',
                                      str_replace(APPPATH . 'classes/Controller/Widget/', '', substr($file, 0, -4)));
            include_once $file;

            $class = new ReflectionClass('Controller_Widget_' . $nameWidget);
            if ($class->isInstantiable()) {
                $widgets[] = $nameWidget;
            }
        }
        return $widgets;
    }

    public function action_main()
    {
        $controllers = $this->getWidgets();
        $widgets     = array();
        foreach ($controllers as $controller) {
            $name           = "Controller_Widget_" . $controller;
            $size           = $name::getPreferredSize();
            $availableSizes = $name::getOptimizedSizes();
            $widgets[]      = array(
                "widget"         => $controller,
                "size"           => $size,
                "availableSizes" => $availableSizes
            );
        }

        $view = View::factory('widgets')
                ->set('from', 'sample')
                ->set('widgets', $widgets);

        $this->response->body($view);
    }

    public function action_info()
    {
        $name           = "Controller_Widget_" . $this->request->param('id');
        $size           = $name::getPreferredSize();
        $availableSizes = $name::getOptimizedSizes();
        $params         = $name::getExpectedParameters('main');
        $widget         = array(
            "widget"         => $this->request->param('id'),
            "size"           => $size,
            "availableSizes" => $availableSizes,
            "params"         => $params
        );
        $this->response->body(json_encode($widget));
    }
}
