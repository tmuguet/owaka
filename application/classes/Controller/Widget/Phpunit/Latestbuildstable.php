<?php

/**
 * Displays the number of errors and failures of the latest 10 builds.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Latestbuildstable extends Controller_Widget_Basetable
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
                'required' => false
            ),
        );
    }

    /**
     * Gets the widget icon
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_RIGHT2;
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
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        if ($this->getProject() === NULL) {
            $builds = ORM::factory('Build')
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->order_by('id', 'DESC')
                    ->with('phpunit_globaldata')
                    ->limit(10)
                    ->find_all();

            $this->process($builds);
        } else {
            $this->display_project();
        }
    }

    /**
     * Processes the widget for project dashboard
     */
    public function display_project()
    {
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(10)
                ->find_all();

        $this->process($builds);
    }

    /**
     * Processes the widget
     * @param Model_Build[] $builds    Builds to process, from latest to oldest
     */
    protected function process($builds)
    {
        $this->columnsHeaders = array(
            "Revision", "Status"
        );

        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => Owaka::getReportUri($builds[0]->id, 'phpunit', 'report')
            );
        }

        foreach ($builds as $build) {
            $status = '';

            if ($build->status == "building") {
                $status = 'ETA ' . date("H:i", strtotime($build->eta));
            } else if (!$build->phpunit_globaldata->loaded()) {
                $status .= View::factory('icon')->set('status', 'nodata')->set('size', 16)->render();
            } else if ($build->phpunit_globaldata->failures > 0 || $build->phpunit_globaldata->errors > 0) {
                if ($build->phpunit_globaldata->failures > 0) {
                    $status .= View::factory('icon')->set('status', 'unstable')->set('size', 16)->render();
                    $status .= $build->phpunit_globaldata->failures;
                }
                if ($build->phpunit_globaldata->failures > 0 && $build->phpunit_globaldata->errors > 0) {
                    $status .= ' ';
                }
                if ($build->phpunit_globaldata->errors > 0) {
                    $status .= View::factory('icon')->set('status', 'error')->set('size', 16)->render();
                    $status .= $build->phpunit_globaldata->errors;
                }
            } else {
                $status .= View::factory('icon')->set('status', 'ok')->set('size', 16)->render();
                $status .= $build->phpunit_globaldata->tests;
            }

            $this->rows[] = array(
                "link"    => array(
                    "type" => 'build',
                    "id"   => $build->id
                ),
                "class"   => 'clickable build build-' . $build->status,
                "columns" => array(
                    $build->getRevision(),
                    $status
                ),
            );
        }
    }
}