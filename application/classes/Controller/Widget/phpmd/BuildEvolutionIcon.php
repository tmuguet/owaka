<?php

class Controller_Widget_phpmd_BuildEvolutionIcon extends Controller_Widget_BaseIcon
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

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('phpmd_globaldata')
                ->find();

        $this->process($build, $prevBuild, TRUE);
    }

    public function sample_all()
    {
        $build                           = ORM::factory('Build');
        $build->phpmd_globaldata->errors = 10;

        $prevBuild                           = ORM::factory('Build');
        $prevBuild->phpmd_globaldata->errors = 8;

        $this->process($build, $prevBuild, TRUE);
    }

    protected function process(Model_Build &$build, Model_Build &$prevBuild, $forceShow = FALSE)
    {
        if ((!$build->phpmd_globaldata->loaded() || !$prevBuild->phpmd_globaldata->loaded()) && !$forceShow) {
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
            
            $errors = $build->phpmd_globaldata->errors - $prevBuild->phpmd_globaldata->errors;

            if ($errors == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                $this->status          = ($errors > 0 ? 'error' : 'ok');
                $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                $this->statusDataLabel = 'errors';
            }
        }
    }
}