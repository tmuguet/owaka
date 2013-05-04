<?php

class Controller_Widget_codesniffer_LastBuildIcon extends Controller_Widget_BaseIcon
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'security';
        $this->widgetTitle = 'codesniffer';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->with('codesniffer_globaldata')
                ->limit(1)
                ->find();

        if (!$build->codesniffer_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors == 0) {
            $this->status          = 'ok';
        } else if ($build->codesniffer_globaldata->warnings > 0 && $build->codesniffer_globaldata->errors == 0) {
            $this->status          = 'unstable';
            $this->statusData      = $build->codesniffer_globaldata->warnings;
            $this->statusDataLabel = 'rules warnings';
        } else if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors > 0) {
            $this->status          = 'error';
            $this->statusData      = $build->codesniffer_globaldata->errors;
            $this->statusDataLabel = 'rules errors';
        } else {
            $this->status             = 'error';
            $this->statusData         = $build->codesniffer_globaldata->errors;
            $this->statusDataLabel    = 'errors';
            $this->substatus          = 'unstable';
            $this->substatusData      = $build->codesniffer_globaldata->warnings;
            $this->substatusDataLabel = 'warnings';
        }

        $this->render();
    }
}