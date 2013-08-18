<?php

/**
 * Displays the status of the latest 10 builds.
 * 
 * @package Widgets
 */
class Controller_Widget_Latestbuildstable extends Controller_Widget_Basetable
{

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
        return 'list';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'builds';
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
            "Project", "Date", "_Date", "Status"
        );

        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $builds[0]->id
            );
        }

        foreach ($builds as $build) {
            $date         = ($build->status == 'building' || $build->status == 'queued') ? $build->started : $build->finished;
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