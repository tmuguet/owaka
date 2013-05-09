<?php

class Controller_Designer_details extends Controller
{

    public function action_main()
    {
        $this->process();
    }

    public function action_project()
    {
        $this->process();
    }

    public function action_build()
    {
        $this->process();
    }

    protected function process()
    {
        $name           = "Controller_Widget_" . $this->request->param('id');
        $size           = $name::getPreferredSize();
        $availableSizes = $name::getOptimizedSizes();
        $params         = $name::getExpectedParameters($this->request->action());
        $post = $this->request->post();
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