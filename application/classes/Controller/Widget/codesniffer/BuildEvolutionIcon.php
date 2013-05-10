<?php

class Controller_Widget_codesniffer_BuildEvolutionIcon extends Controller_Widget_BaseIcon
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

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('codesniffer_globaldata')
                ->find();

        $this->process($build, $prevBuild);
    }

    public function sample_all()
    {
        $build                                   = ORM::factory('Build');
        $build->codesniffer_globaldata->errors   = 10;
        $build->codesniffer_globaldata->warnings = 6;

        $prevBuild                                   = ORM::factory('Build');
        $prevBuild->codesniffer_globaldata->errors   = 8;
        $prevBuild->codesniffer_globaldata->warnings = 7;

        $this->process($build, $prevBuild, TRUE);
    }

    protected function process(Model_Build &$build, Model_Build &$prevBuild, $forceShow = FALSE)
    {
        if ((!$build->codesniffer_globaldata->loaded() || !$prevBuild->codesniffer_globaldata->loaded()) && !$forceShow) {
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
            
            $errors   = $build->codesniffer_globaldata->errors - $prevBuild->codesniffer_globaldata->errors;
            $warnings = $build->codesniffer_globaldata->warnings - $prevBuild->codesniffer_globaldata->warnings;

            if ($errors == 0 && $warnings == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                $this->widgetStatus    = ($errors > 0 || $warnings > 0 ? 'unstable' : 'ok');
                $this->status          = ($errors > 0 ? 'error' : 'ok');
                $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                $this->statusDataLabel = 'rules errors';

                $this->substatus          = ($warnings > 0 ? 'unstable' : 'ok');
                $this->substatusData      = ($warnings > 0 ? '+' . $warnings : $warnings);
                $this->substatusDataLabel = 'rules warnings';
            }
        }
    }
}