<?php

class Controller_Widget_phpunit_LatestBuildsSparklines extends Controller_Widget_BaseSparklines
{

    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            ),
        );
    }

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

        $this->process($builds);
    }

    public function action_sample()
    {
        $builds = array();
        $t      = 1042;
        $f      = 1;
        $e      = 0;
        for ($i = 0; $i < 50; $i++) {
            $build                               = ORM::factory('Build');
            $build->phpunit_globaldata->tests    = $t                                   = max(0, $t + rand(-10, 5));
            $build->phpunit_globaldata->errors   = $e                                   = max(0, $e + rand(-2, 2));
            $build->phpunit_globaldata->failures = $f                                   = max(0, $f + rand(-3, 3));
            $builds[]                            = $build;
        }

        $this->process($builds, TRUE);
    }

    protected function process($builds, $forceShow = FALSE)
    {
        $this->widgetLinks[] = array(
            "title" => 'latest report',
            "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
        );

        $tests    = array();
        $failures = array();
        $errors   = array();

        foreach ($builds as $build) {
            if ($build->phpunit_globaldata->loaded() || $forceShow) {
                $tests[]    = $build->phpunit_globaldata->tests;
                $failures[] = $build->phpunit_globaldata->failures;
                $errors[]   = $build->phpunit_globaldata->errors;
            }
        }

        $this->sparklines[] = array("title" => "Tests", "data"  => array_reverse($tests));
        $this->sparklines[] = array("title" => "Failures", "data"  => array_reverse($failures));
        $this->sparklines[] = array("title" => "Errors", "data"  => array_reverse($errors));

        $this->render();
    }
}