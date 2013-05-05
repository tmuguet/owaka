<?php

class Controller_Widget_phpmd_LastBuildIcon extends Controller_Widget_BaseIcon
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'flag';
        $this->widgetTitle = 'phpmd';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('phpmd_globaldata')
                ->limit(1)
                ->find();

        if (!$build->phpmd_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else if ($build->phpmd_globaldata->errors == 0) {
            $this->status          = 'ok';
        } else {
            $this->status          = 'unstable';
            $this->statusData      = $build->phpmd_globaldata->errors;
            $this->statusDataLabel = 'errors';
        }

        $this->render();
    }
}