<?php

class Controller_Widget_Queue extends Controller_Widget_BaseTable
{

    public function before()
    {
        $this->widgetIcon = 'clock';
        $this->widgetTitle = 'queue';
    }

    public function action_main()
    {
        $this->columnsHeaders = array(
            "Project", "Status"
        );

        $builds = ORM::factory('Build')
                ->where('status', 'IN', array('queued', 'building'))
                ->order_by('status', 'ASC')
                ->order_by('started', 'ASC')
                ->find_all();

        foreach ($builds as $build) {
            if ($build->status == "building") {
                $status = 'ETA ' . date("H:i", strtotime($build->eta));
            } else {
                $status = '';
            }

            $this->rows[] = array(
                "url"     => "",
                "columns" => array(
                    $build->project->name,
                    $status
                ),
            );
        }

        $this->render();
    }
}