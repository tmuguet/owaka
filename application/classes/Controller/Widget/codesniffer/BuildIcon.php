<?php

class Controller_Widget_codesniffer_BuildIcon extends Controller_Widget_BaseIcon
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
        $this->widgetIcon  = 'security';
        $this->widgetTitle = 'codesniffer';
    }

    public function display_all()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->with('codesniffer_globaldata')
                    ->find();
        }

        $this->process($build);
    }

    public function sample_all()
    {
        $build                                   = ORM::factory('Build');
        $build->codesniffer_globaldata->errors   = 10;
        $build->codesniffer_globaldata->warnings = 6;

        $this->process($build, TRUE);
    }

    protected function process(Model_Build &$build, $forceShow = FALSE)
    {
        if (!$build->codesniffer_globaldata->loaded() && !$forceShow) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/codesniffer/index.xml'
            );

            if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors == 0) {
                $this->status = 'ok';
            } else if ($build->codesniffer_globaldata->warnings > 0 && $build->codesniffer_globaldata->errors == 0) {
                $this->status          = 'unstable';
                $this->statusData      = $build->codesniffer_globaldata->warnings;
                $this->statusDataLabel = 'rules warnings';
            } else if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors > 0) {
                $this->status          = 'error';
                $this->statusData      = $build->codesniffer_globaldata->errors;
                $this->statusDataLabel = 'rules errors';
            } else {
                $this->widgetStatus       = 'error';
                $this->status             = 'error';
                $this->statusData         = $build->codesniffer_globaldata->errors;
                $this->statusDataLabel    = 'errors';
                $this->substatus          = 'unstable';
                $this->substatusData      = $build->codesniffer_globaldata->warnings;
                $this->substatusDataLabel = 'warnings';
            }
        }
    }
}