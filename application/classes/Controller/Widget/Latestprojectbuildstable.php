<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the status of the latest builds for all projects.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Latestprojectbuildstable extends Controller_Widget_Basetable
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
        return array();
    }

    /**
     * Gets the widget icon
     * 
     * @return string
     */
    protected function getWidgetIcon()
    {
        return 'list';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'builds/project';
    }

    /**
     * Processes the widget for all dashboards
     */
    public function display_all()
    {
        $projects = ORM::factory('Project')
                ->where('is_active', '=', 1)
                ->order_by('name', 'ASC')
                ->find_all();

        $builds = array();
        foreach ($projects as $project) {
            $builds[] = $project->lastBuild()->find();
        }
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
            "Project", "Date", "_Date", "Status"
        );

        foreach ($builds as $build) {
            if ($build->loaded()) {
                $date = ($build->status == Owaka::BUILD_BUILDING || $build->status == Owaka::BUILD_QUEUED) ? $build->started : $build->finished;

                $this->rows[] = array(
                    "link"    => array(
                        "type" => 'build',
                        "id"   => $build->id
                    ),
                    "class"   => 'clickable build build-' . $build->status,
                    "columns" => array(
                        $build->project->name,
                        Date::loose_span(strtotime($date)),
                        $date,
                        View::factory('icon')->set('status', $build->status)->render()
                    ),
                );
            }
        }
    }
}