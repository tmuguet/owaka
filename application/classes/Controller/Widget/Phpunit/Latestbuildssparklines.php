<?php

/**
 * Displays the number of errors and failures of the latest 50 builds.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Latestbuildssparklines extends Controller_Widget_Basesparklines
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
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        return $this->display_project();
    }

    /**
     * Processes the widget for project dashboard
     */
    public function display_project()
    {
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(50)
                ->find_all();

        $this->process($builds);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build[] $builds Builds to process, from latest to oldest
     */
    protected function process($builds)
    {
        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                "title" => 'latest report',
                "url"   => Owaka::getReportUri($builds[0]->id, 'phpunit', 'report')
            );
        }

        $tests    = array();
        $failures = array();
        $errors   = array();

        foreach ($builds as $build) {
            if ($build->phpunit_globaldata->loaded()) {
                $tests[]    = $build->phpunit_globaldata->tests;
                $failures[] = $build->phpunit_globaldata->failures;
                $errors[]   = $build->phpunit_globaldata->errors;
            }
        }

        $this->sparklines[] = array("title" => "Tests", "data"  => array_reverse($tests));
        $this->sparklines[] = array("title" => "Failures", "data"  => array_reverse($failures));
        $this->sparklines[] = array("title" => "Errors", "data"  => array_reverse($errors));
    }
}