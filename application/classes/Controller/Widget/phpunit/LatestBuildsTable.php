<?php

/**
 * Displays the number of errors and failures of the latest 10 builds.
 */
class Controller_Widget_phpunit_LatestBuildsTable extends Controller_Widget_BaseTable
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
                'required' => false
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
        if ($this->getProject() === NULL) {
            $builds = ORM::factory('Build')
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->order_by('id', 'DESC')
                    ->with('phpunit_globaldata')
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
                ->with('phpunit_globaldata')
                ->limit(10)
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
        $f      = 0;
        $e      = 0;
        for ($i = 0; $i < 10; $i++) {
            $build                               = ORM::factory('Build');
            $build->revision                     = 10 - $i;
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
        $this->columnsHeaders = array(
            "Revision", "Status"
        );

        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
            );
        }

        foreach ($builds as $build) {
            $status = '';

            if ($build->status == "building") {
                $status = 'ETA ' . date("H:i", strtotime($build->eta));
            } else if (!$build->phpunit_globaldata->loaded() && !$forceShow) {
                $status .= View::factory('icon')->set('status', 'nodata')->set('size', 16)->render();
            } else if ($build->phpunit_globaldata->failures > 0 || $build->phpunit_globaldata->errors > 0) {
                if ($build->phpunit_globaldata->failures > 0) {
                    $status .= View::factory('icon')->set('status', 'unstable')->set('size', 16)->render();
                    $status .= $build->phpunit_globaldata->failures;
                }
                if ($build->phpunit_globaldata->failures > 0 && $build->phpunit_globaldata->errors > 0) {
                    $status .= ' ';
                }
                if ($build->phpunit_globaldata->errors > 0) {
                    $status .= View::factory('icon')->set('status', 'error')->set('size', 16)->render();
                    $status .= $build->phpunit_globaldata->errors;
                }
            } else {
                $status .= View::factory('icon')->set('status', 'ok')->set('size', 16)->render();
                $status .= $build->phpunit_globaldata->tests;
            }

            $this->rows[] = array(
                "link"    => array(
                    "type" => 'build',
                    "id"   => $build->id
                ),
                "class"   => 'clickable build build-' . $build->status,
                "columns" => array(
                    'r' . $build->revision,
                    $status
                ),
            );
        }
    }
}