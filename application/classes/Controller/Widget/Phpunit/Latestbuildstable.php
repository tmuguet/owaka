<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the number of errors and failures of the latest 10 builds.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpunit_Latestbuildstable extends Controller_Widget_Table
{

    public static $icon        = 'check';
    public static $title       = 'PHPUnit';
    protected static $autorefresh = TRUE;

    /**
     * Gets the expected parameters
     * 
     * @param string $dashboard Type of dashboard
     * 
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    static public function expectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => false
            ),
        );
    }

    /**
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        $this->display_project();
    }

    /**
     * Processes the widget for project dashboard
     */
    public function display_project()
    {
        $builds = $this->getLastBuilds(10);
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
            'Revision', 'Status'
        );

        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                'title' => 'report',
                'url'   => Owaka::getReportUri($builds[0]->id, 'phpunit', 'report')
            );
        }

        foreach ($builds as $build) {
            $parameters = Owaka::getReportParameters($build->project_id, 'phpunit');
            $status     = '';

            if ($build->status == Owaka::BUILD_BUILDING) {
                $status = 'ETA ' . date('H:i', strtotime($build->eta));
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
                'link'    => array(
                    'type' => 'build',
                    'id'   => $build->id
                ),
                'class'   => 'clickable build build-' . $build->status,
                'columns' => array(
                    $build->getRevision(),
                    $status
                ),
            );
        }
    }
}