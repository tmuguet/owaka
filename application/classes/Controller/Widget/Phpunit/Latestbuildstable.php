<?php

/**
 * Displays the number of errors and failures of the latest 10 builds.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Latestbuildstable extends Controller_Widget_Basetable
{

    protected $autorefresh = TRUE;

    /**
     * Gets the expected parameters
     * 
     * //@param string $dashboard Type of dashboard
     * 
     * @return array
     */
    static public function getExpectedParameters(/* $dashboard */)
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
        if ($this->getProject() === NULL) {
            $builds = ORM::factory('Build')
                    ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
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
                ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(10)
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
            $parameters = Owaka::getReportParameters($build->project_id, 'phpunit');
            $status     = '';

            if ($build->status == Owaka::BUILD_BUILDING) {
                $status = 'ETA ' . date("H:i", strtotime($build->eta));
            } else if (!$build->phpunit_globaldata->loaded()) {
                $status .= View::factory('icon')->set('status', Owaka::BUILD_NODATA)->render();
            } else {
                $status .= View::factory('icon')->set('status', $build->phpunit_globaldata->buildStatus($parameters))->render();
                if ($build->phpunit_globaldata->errors > 0) {
                    $status .= $build->phpunit_globaldata->errors;
                } else if ($build->phpunit_globaldata->failures > 0) {
                    $status .= $build->phpunit_globaldata->failures;
                } else {
                    $status .= $build->phpunit_globaldata->tests;
                }
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