<?php

class Controller_Widget_phpunit_BuildIcon extends Controller_Widget_BaseIcon
{

    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            ),
            'build'   => array(
                'type'     => 'build',
                'required' => false,
            )
        );
    }

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'pad';
        $this->widgetTitle = 'phpunit';
    }

    public function action_main()
    {
        return $this->action_build();
    }

    public function action_project()
    {
        return $this->action_build();
    }

    public function action_build()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->with('phpunit_globaldata')
                    ->find();
        }
        $this->process($build);
    }

    public function action_sample()
    {
        $build                               = ORM::factory('Build');
        $build->phpunit_globaldata->tests    = 2042;
        $build->phpunit_globaldata->errors   = 1;
        $build->phpunit_globaldata->failures = 6;

        $this->process($build, TRUE);
    }

    protected function process(Model_Build &$build, $forceShow = FALSE)
    {
        if (!$build->phpunit_globaldata->loaded() && !$forceShow) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/phpunit/index.html'
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