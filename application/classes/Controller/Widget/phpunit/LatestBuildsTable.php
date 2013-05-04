<?php

class Controller_Widget_phpunit_LatestBuildsTable extends Controller_Widget_BaseTable
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
        $this->columnsHeaders = array(
            "Revision", "Status"
        );

        $builds = $this->getProject()->builds
                ->order_by('id', 'DESC')
                ->with('phpunit_globaldata')
                ->limit(10)
                ->find_all();

        $this->widgetLinks[] = array(
            "title" => 'latest report',
            "url"   => 'reports/' . $builds[0]->id . '/phpunit/index.html'
        );

        foreach ($builds as $build) {
            $status = '';

            if ($build->status == "building") {
                $status = 'ETA ' . date("H:i", strtotime($build->eta));
            } else if (!$build->phpunit_globaldata->loaded()) {
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
                "url"     => 'welcome/build/' . $build->id,
                "class"   => 'clickable build build-' . $build->status,
                "columns" => array(
                    'r' . $build->revision,
                    $status
                ),
            );
        }

        $this->render();
    }
}