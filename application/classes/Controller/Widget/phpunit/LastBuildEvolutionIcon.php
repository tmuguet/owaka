<?php

class Controller_Widget_phpunit_LastBuildEvolutionIcon extends Controller_Widget_BaseIcon
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'pad';
        $this->widgetTitle = 'phpunit';
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
                ->with('phpunit_globaldata')
                ->limit(2)
                ->find_all();

        if ($builds->count() != 2 || !$builds[0]->phpunit_globaldata->loaded() || !$builds[1]->phpunit_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
            );

            $errors   = $builds[0]->phpunit_globaldata->errors - $builds[1]->phpunit_globaldata->errors;
            $failures = $builds[0]->phpunit_globaldata->failures - $builds[1]->phpunit_globaldata->failures;

            if ($errors == 0 && $failures == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                if ($errors < 0 && $failures < 0) {
                    $this->widgetStatus = 'ok';
                } else if ($errors > 0) {
                    $this->widgetStatus = 'error';
                } else {
                    $this->widgetStatus = 'unstable';
                }

                if ($errors == 0) {
                    $this->status          = 'ok';
                    $this->statusData      = '-';
                    $this->statusDataLabel = '<br>no changes';
                } else {
                    $this->status          = ($errors > 0 ? 'error' : 'ok');
                    $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                    $this->statusDataLabel = 'tests errored';
                }

                if ($failures == 0) {
                    $this->substatus          = 'ok';
                    $this->substatusData      = '-';
                    $this->substatusDataLabel = '<br>no changes';
                } else {
                    $this->substatus          = ($failures > 0 ? 'unstable' : 'ok');
                    $this->substatusData      = ($failures > 0 ? '+' . $failures : $failures);
                    $this->substatusDataLabel = 'tests failed';
                }
            }
        }

        $this->render();
    }
}