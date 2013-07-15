<?php

/**
 * Displays the building queue.
 * 
 * @package Widgets
 */
class Controller_Widget_Queue extends Controller_Widget_Basetable
{

    /**
     * Gets the expected parameters
     * @param string $dashboard Type of dashboard
     * @return array
     */
    static public function getExpectedParameters($dashboard)
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
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_CLOCK;
    }

    /**
     * Gets the widget title
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
                ->where('status', 'IN', array('queued', 'building'))
                ->order_by('status', 'ASC')
                ->order_by('started', 'ASC')
                ->order_by('id', 'ASC')
                ->limit($this->getParameter('max'))
                ->find_all();

        $this->process($builds);
    }

    /**
     * Processes the widget
     * @param Model_Build[] $builds Builds to process
     */
    protected function process($builds)
    {

        $this->columnsHeaders = array(
            "Project", "Status", "_Date"
        );
        foreach ($builds as $build) {
            if ($build->status == "building") {
                if (strtotime($build->eta) < time()) {
                    $status = 'unknown';
                } else {
                    $status = Date::loose_span(strtotime($build->eta));
                }
                $date = $build->eta;
            } else {
                $status = '';
                $date = $build->started;
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