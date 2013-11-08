<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the status of the latest 10 builds.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Latestbuildstable extends Controller_Widget_Table
{

    public static $icon  = 'list';
    public static $title = 'Builds';
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
            'Project', 'Date', '_Date', 'Status'
        );

        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $builds[0]->id
            );
        }

        foreach ($builds as $build) {
            $this->rows[] = array(
                'link'    => array(
                    'type' => 'build',
                    'id'   => $build->id
                ),
                'class'   => 'clickable build build-' . $build->status,
                'columns' => array(
                    $build->project->name,
                    Date::loose_span(strtotime($build->finished)),
                    $build->finished,
                    View::factory('icon')->set('status', $build->status)->render()
                ),
            );
        }
    }
}