<?php

/**
 * Displays the number of errors and failures of a build.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Buildicon extends Controller_Widget_Baseicon
{

    /**
     * Gets the expected parameters
     * 
     * @param string $dashboard Type of dashboard
     * 
     * @return array
     */
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

    /**
     * Gets the widget icon
     * 
     * @return string
     */
    protected function getWidgetIcon()
    {
        return 'check';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'phpunit';
    }

    /**
     * Processes the widget for all dashboards
     */
    public function display_all()
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

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build Current build to process
     */
    protected function process(Model_Build &$build)
    {
        if (!$build->phpunit_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => Owaka::getReportUri($build->id, 'phpunit', 'report')
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
    }
}