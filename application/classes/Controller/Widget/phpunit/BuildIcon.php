<?php

/**
 * Displays the number of errors and failures of a build.
 */
class Controller_Widget_phpunit_BuildIcon extends Controller_Widget_BaseIcon
{

    /**
     * Gets the expected parameters
     * @param string $dashboard Type of dashboard
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
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_PAD;
    }

    /**
     * Gets the widget title
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
     * Processes the widget for sample in all dashboards
     */
    public function sample_all()
    {
        $build                               = ORM::factory('Build');
        $build->phpunit_globaldata->tests    = 2042;
        $build->phpunit_globaldata->errors   = 1;
        $build->phpunit_globaldata->failures = 6;

        $this->process($build, TRUE);
    }

    /**
     * Processes the widget
     * @param Model_Build $build     Current build to process
     * @param bool        $forceShow Force showing widget when model is not loaded
     */
    protected function process(Model_Build &$build, $forceShow = FALSE)
    {
        if (!$build->phpunit_globaldata->loaded() && !$forceShow) {
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