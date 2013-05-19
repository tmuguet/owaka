<?php

/**
 * Gets details of a widget for the designer mode
 */
class Controller_Designer_details extends Controller
{

    /**
     * Displays details for a main dashboard widget
     * @see process()
     */
    public function action_main()
    {
        $this->process();
    }

    /**
     * Displays details for a project dashboard widget
     * @see process()
     */
    public function action_project()
    {
        $this->process();
    }

    /**
     * Displays details for a build dashboard widget
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
        $name           = "Controller_Widget_" . $this->request->param('id');
        $size           = $name::getPreferredSize();
        $availableSizes = $name::getOptimizedSizes();
        $params         = $name::getExpectedParameters($this->request->action());
        $post           = $this->request->post();
        if (isset($params['project']) && !empty($post['projectId'])) {
            $params['project']['default'] = $post['projectId'];
        }
        if (isset($params['build']) && !empty($post['buildId'])) {
            $params['build']['default'] = $post['buildId'];
        }

        $view = View::factory('designer_widgetdetails')
                ->set('from', $this->request->action())
                ->set('widget', $this->request->param('id'))
                ->set('size', $size)
                ->set('availableSizes', $availableSizes)
                ->set('params', $params);
        $this->response->body($view);
    }
}