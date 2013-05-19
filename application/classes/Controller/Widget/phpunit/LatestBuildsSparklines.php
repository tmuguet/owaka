<?php

/**
 * Displays the number of errors and failures of the latest 50 builds.
 */
class Controller_Widget_phpunit_LatestBuildsSparklines extends Controller_Widget_BaseSparklines
{

    /**
     * Gets the expected parameters
     * @param string $dashboard Type of dashboard
     * @return array
     */
    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            ),
        );
    }

    /**
     * Gets the widget icon
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_PAD;
    }

    /**
     * Gets the widget title
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'phpunit';
    }

    /**
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        return $this->display_project();
    }

    /**
     * Processes the widget for project dashboard
     */
    public function display_project()
    {
        $builds = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(50)
                ->find_all();

        $this->process($builds);
    }

    /**
     * Processes the widget for sample in main dashboard
     */
    public function sample_main()
    {
        return $this->sample_project();
    }

    /**
     * Processes the widget for sample in project dashboard
     */
    public function sample_project()
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

    /**
     * Processes the widget
     * @param Model_Build[] $builds    Builds to process, from latest to oldest
     * @param bool          $forceShow Force showing widget when model is not loaded
     */
    protected function process($builds, $forceShow = FALSE)
    {
        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                "title" => 'latest report',
                "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
            );
        }

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
    }
}