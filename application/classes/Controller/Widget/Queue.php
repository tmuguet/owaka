<?php

class Controller_Widget_Queue extends Controller_Widget_BaseTable
{

    static public function getPreferredSize()
    {
        return parent::getPreferredSize();
    }

    static public function getOptimizedSizes()
    {
        return parent::getOptimizedSizes();
    }

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

    public function before()
    {
        $this->widgetIcon  = 'clock';
        $this->widgetTitle = 'queue';
    }

    public function action_main()
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

    public function action_sample()
    {
        $builds               = array();
        $build                = ORM::factory('Build');
        $build->status        = "building";
        $build->project->name = "foo";
        $build->eta           = time() + rand(180, 3600);
        $builds[]             = $build;

        for ($i = 0; $i < 3; $i++) {
            $build                = ORM::factory('Build');
            $build->project->name = "bar " . $i;
            $builds[]             = $build;
        }

        $this->process($builds);
    }

    protected function process($builds)
    {

        $this->columnsHeaders = array(
            "Project", "Status"
        );
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