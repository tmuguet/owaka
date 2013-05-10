<?php

class Controller_Widget_phpunit_BuildEvolutionIcon extends Controller_Widget_BaseIcon
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

    public function display_all()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->with('phpunit_globaldata')
                    ->find();
        }

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('phpunit_globaldata')
                ->find();

        $this->process($build, $prevBuild);
    }

    public function sample_all()
    {
        $build                               = ORM::factory('Build');
        $build->phpunit_globaldata->tests    = 1200;
        $build->phpunit_globaldata->errors   = 0;
        $build->phpunit_globaldata->failures = 6;

        $prevBuild                               = ORM::factory('Build');
        $prevBuild->phpunit_globaldata->tests    = 1178;
        $prevBuild->phpunit_globaldata->errors   = 0;
        $prevBuild->phpunit_globaldata->failures = 7;

        $this->process($build, $prevBuild, TRUE);
    }

    protected function process(Model_Build &$build, Model_Build &$prevBuild, $forceShow = FALSE)
    {
        if ((!$build->phpunit_globaldata->loaded() || !$prevBuild->phpunit_globaldata->loaded()) && !$forceShow) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/phpunit/index.html'
            );

            $errors   = $build->phpunit_globaldata->errors - $prevBuild->phpunit_globaldata->errors;
            $failures = $build->phpunit_globaldata->failures - $prevBuild->phpunit_globaldata->failures;

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
    }
}