<?php

class Controller_Widget_phpunit_LatestBuildsSparklines extends Controller_Widget_BaseSparklines
{

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'pad';
        $this->widgetTitle = 'phpunit';
    }

    public function action_main()
    {
        return $this->action_project();
    }

    public function action_project()
    {
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(50)
                ->find_all();

        $this->widgetLinks[] = array(
            "title" => 'latest report',
            "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
        );

        $tests    = array();
        $failures = array();
        $errors   = array();

        foreach ($builds as $build) {
            if ($build->phpunit_globaldata->loaded()) {
                $tests[]    = $build->phpunit_globaldata->tests;
                $failures[] = $build->phpunit_globaldata->failures;
                $errors[]   = $build->phpunit_globaldata->errors;
            }
        }

        $this->sparklines[] = array("title" => "Tests", "data"  => $tests);
        $this->sparklines[] = array("title" => "Failures", "data"  => $failures);
        $this->sparklines[] = array("title" => "Errors", "data"  => $errors);

        $this->render();
    }
}