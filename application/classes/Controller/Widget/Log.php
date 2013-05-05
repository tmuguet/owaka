<?php

class Controller_Widget_Log extends Controller_Widget_BaseRaw
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'doc';
        $this->widgetTitle = 'log';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();

        if (!file_exists(APPPATH . 'reports/' . $build->id . '/log.html')) {
            $this->content = 'No data';
        } else {
            $this->content = file_get_contents(APPPATH . 'reports/' . $build->id . '/log.html');
        }

        $this->render();
    }
}