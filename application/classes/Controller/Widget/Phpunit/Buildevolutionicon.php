<?php

/**
 * Displays the evolution of the number of errors and failures with previous build.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Buildevolutionicon extends Controller_Widget_Baseicon
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

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('phpunit_globaldata')
                ->find();

        $this->process($build, $prevBuild);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build     Current build to process
     * @param Model_Build &$prevBuild Previous build to process
     */
    protected function process(Model_Build &$build, Model_Build &$prevBuild)
    {
        if (!$build->phpunit_globaldata->loaded() || !$prevBuild->phpunit_globaldata->loaded()) {
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

            $errors   = $build->phpunit_globaldata->errors - $prevBuild->phpunit_globaldata->errors;
            $failures = $build->phpunit_globaldata->failures - $prevBuild->phpunit_globaldata->failures;

            if ($errors == 0 && $failures == 0) {
                $this->status          = Owaka::BUILD_OK;
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                if ($errors <= 0 && $failures <= 0) {
                    $this->widgetStatus = Owaka::BUILD_OK;
                } else if ($errors > 0) {
                    $this->widgetStatus = Owaka::BUILD_ERROR;
                } else {
                    $this->widgetStatus = Owaka::BUILD_UNSTABLE;
                }

                if ($errors == 0) {
                    $this->status          = Owaka::BUILD_OK;
                    $this->statusData      = '-';
                    $this->statusDataLabel = '<br>no changes';
                } else {
                    $this->status          = ($errors > 0 ? Owaka::BUILD_ERROR : Owaka::BUILD_OK);
                    $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                    $this->statusDataLabel = 'tests errored';
                }

                if ($failures == 0) {
                    $this->substatus          = Owaka::BUILD_OK;
                    $this->substatusData      = '-';
                    $this->substatusDataLabel = '<br>no changes';
                } else {
                    $this->substatus          = ($failures > 0 ? Owaka::BUILD_UNSTABLE : Owaka::BUILD_OK);
                    $this->substatusData      = ($failures > 0 ? '+' . $failures : $failures);
                    $this->substatusDataLabel = 'tests failed';
                }
            }
        }
    }
}