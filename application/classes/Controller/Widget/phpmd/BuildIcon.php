<?php

class Controller_Widget_phpmd_BuildIcon extends Controller_Widget_BaseIcon
{

    static public function getExpectedParameters($dashboard)
    {
        return array(
            'project' => array(
                'type'     => 'project',
                'required' => ($dashboard == 'main')
            ),
            'build'   => array(
                'type'     => 'build',
                'required' => false,
            )
        );
    }

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'flag';
        $this->widgetTitle = 'phpmd';
    }

    public function display_all()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->with('phpmd_globaldata')
                    ->find();
        }

        $this->process($build);
    }

    public function sample_all()
    {
        $build                           = ORM::factory('Build');
        $build->phpmd_globaldata->errors = 10;

        $this->process($build, TRUE);
    }

    protected function process(Model_Build &$build, $forceShow = FALSE)
    {

        if (!$build->phpmd_globaldata->loaded() && !$forceShow) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/phpmd/index.html'
            );

            if ($build->phpmd_globaldata->errors == 0) {
                $this->status = 'ok';
            } else {
                $this->status          = 'unstable';
                $this->statusData      = $build->phpmd_globaldata->errors;
                $this->statusDataLabel = 'errors';
            }
        }
    }
}