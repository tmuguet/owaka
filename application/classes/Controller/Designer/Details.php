<?php
defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Gets details of a widget for the designer mode
 * 
 * @package   Designer
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Designer_Details extends Controller
{

    /**
     * Displays details for a main dashboard widget
     * 
     * @see process()
     */
    public function action_main()
    {
        $this->process();
    }

    /**
     * Displays details for a project dashboard widget
     * 
     * @see process()
     */
    public function action_project()
    {
        $this->process();
    }

    /**
     * Displays details for a build dashboard widget
     * 
     * @see process()
     */
    public function action_build()
    {
        $this->process();
    }

    /**
     * Displays details for a widget
     */
    protected function process()
    {
        $name = 'Controller_Widget_' . $this->request->param('id');
        if (!class_exists($name)) {
            throw new HTTP_Exception_404();
        }
        $size           = $name::getPreferredSize();
        $availableSizes = $name::getOptimizedSizes();
        $params         = $name::getExpectedParameters($this->request->action());
        $post           = $this->request->post();
        if (isset($params['project']) && !empty($post['projectId'])) {
            $params['project']['default'] = $post['projectId'];
        }

        $view = View::factory('designer_widgetdetails')
                ->set('from', $this->request->action())
                ->set('widget', $this->request->param('id'))
                ->set('size', $size)
                ->set('availableSizes', $availableSizes)
                ->set('params', $params);
        $this->success($view);
    }
}