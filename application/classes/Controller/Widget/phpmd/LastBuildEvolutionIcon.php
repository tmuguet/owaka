<?php

class Controller_Widget_phpmd_LastBuildEvolutionIcon extends Controller_Widget_BaseIcon
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
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('phpmd_globaldata')
                ->limit(2)
                ->find_all();

        if ($builds->count() != 2 || !$builds[0]->phpmd_globaldata->loaded() || !$builds[1]->phpmd_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $errors   = $builds[0]->phpmd_globaldata->errors - $builds[1]->phpmd_globaldata->errors;

            if ($errors == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                $this->status          = ($errors > 0 ? 'error' : 'ok');
                $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                $this->statusDataLabel = 'errors';
            }
        }

        $this->render();
    }
}