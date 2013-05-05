<?php

class Controller_Widget_codesniffer_LastBuildEvolutionIcon extends Controller_Widget_BaseIcon
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
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('codesniffer_globaldata')
                ->limit(2)
                ->find_all();

        if ($builds->count() != 2 || !$builds[0]->codesniffer_globaldata->loaded() || !$builds[1]->codesniffer_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $errors   = $builds[0]->codesniffer_globaldata->errors - $builds[1]->codesniffer_globaldata->errors;
            $warnings = $builds[0]->codesniffer_globaldata->warnings - $builds[1]->codesniffer_globaldata->warnings;

            if ($errors == 0 && $warnings == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                $this->widgetStatus    = ($errors > 0 || $warnings > 0 ? 'unstable' : 'ok');
                $this->status          = ($errors > 0 ? 'error' : 'ok');
                $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                $this->statusDataLabel = 'rules errors';

                $this->substatus          = ($warnings > 0 ? 'unstable' : 'ok');
                $this->substatusData      = ($warnings > 0 ? '+' . $warnings : $warnings);
                $this->substatusDataLabel = 'rules warnings';
            }
        }

        $this->render();
    }
}