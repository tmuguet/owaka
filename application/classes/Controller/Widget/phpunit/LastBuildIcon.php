<?php

class Controller_Widget_phpunit_LastBuildIcon extends Controller_Widget_BaseIcon
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
        $build = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(1)
                ->find();

        if (!$build->phpunit_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"  => 'reports/' . $build->id . '/phpunit/index.html'
            );

            if ($build->phpunit_globaldata->failures == 0 && $build->phpunit_globaldata->errors == 0) {
                $this->status          = 'ok';
                $this->statusData      = $build->phpunit_globaldata->tests;
                $this->statusDataLabel = 'tests passed';
            } else if ($build->phpunit_globaldata->failures > 0 && $build->phpunit_globaldata->errors == 0) {
                $this->status          = 'unstable';
                $this->statusData      = $build->phpunit_globaldata->failures;
                $this->statusDataLabel = 'tests failed<br>out of ' . $build->phpunit_globaldata->tests;
            } else if ($build->phpunit_globaldata->failures == 0 && $build->phpunit_globaldata->errors > 0) {
                $this->status          = 'error';
                $this->statusData      = $build->phpunit_globaldata->errors;
                $this->statusDataLabel = 'tests errored<br>out of ' . $build->phpunit_globaldata->tests;
            } else {
                $this->widgetStatus = 'error';

                $this->status             = 'error';
                $this->statusData         = $build->phpunit_globaldata->errors;
                $this->statusDataLabel    = 'errors';
                $this->substatus          = 'unstable';
                $this->substatusData      = $build->phpunit_globaldata->failures;
                $this->substatusDataLabel = 'failed';
            }
        }

        $this->render();
    }
}