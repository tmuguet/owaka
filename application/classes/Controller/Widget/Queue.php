<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the building queue.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Queue extends Controller_Widget_Basetable
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
            'max' => array(
                'title'      => 'Maximum builds to show',
                'type'       => 'uint',
                'default'    => '10',
                'required'   => false,
                'validation' => array()
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
        return 'time';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Queue';
    }

    /**
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        $builds = ORM::factory('Build')
                ->where('status', 'IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                ->order_by('status', 'ASC')
                ->order_by('started', 'ASC')
                ->order_by('id', 'ASC')
                ->limit($this->getParameter('max'))
                ->find_all();

        $this->process($builds);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build[] $builds Builds to process
     */
    protected function process($builds)
    {

        $this->columnsHeaders = array(
            "Project", "Status", "_Date"
        );
        foreach ($builds as $build) {
            if ($build->status == Owaka::BUILD_BUILDING) {
                if (strtotime($build->eta) < time()) {
                    $status = 'unknown';
                } else {
                    $status = Date::loose_span(strtotime($build->eta));
                }
                $date = $build->eta;
            } else {
                $status = '';
                $date   = $build->started;
            }

            $this->rows[] = array(
                "url"     => "",
                "columns" => array(
                    $build->project->name,
                    $status,
                    $date
                ),
            );
        }
    }
}