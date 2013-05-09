<?php

class Controller_Widget_coverage_BuildIcon extends Controller_Widget_BaseIcon
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
            ),
            'display' => array(
                'title'    => 'Display',
                'type'     => 'enum',
                'enum'     => array('total', 'methods', 'statements', 'methods+statements'),
                'default'  => 'methods+statements',
                'required' => false,
            ),
        );
    }

    public function before()
    {
        parent::before();
        $this->widgetIcon  = 'target';
        $this->widgetTitle = 'coverage';
    }

    public function display_all()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array('building', 'queued'))
                    ->with('coverage_globaldata')
                    ->find();
        }
        $this->process($build);
    }

    public function sample_all()
    {
        $build                                         = ORM::factory('Build');
        $build->coverage_globaldata->totalcoverage     = 98.47;
        $build->coverage_globaldata->methodcoverage    = 97.68;
        $build->coverage_globaldata->statementcoverage = 99.82;

        $this->process($build, TRUE);
    }

    protected function process(Model_Build &$build, $forceShow = FALSE)
    {
        if (!$build->coverage_globaldata->loaded() && !$forceShow) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {

            $display = $this->getParameter('display');

            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/coverage/index.html'
            );

            switch ($display) {
                case 'total':
                    $this->statusData      = floor($build->coverage_globaldata->totalcoverage) . '%';
                    $this->statusDataLabel = '<br/>total';
                    if ($build->coverage_globaldata->totalcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->totalcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;

                case 'methods':
                    $this->statusData      = floor($build->coverage_globaldata->methodcoverage) . '%';
                    $this->statusDataLabel = '<br/>methods';
                    if ($build->coverage_globaldata->methodcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->methodcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;

                case 'statements':
                    $this->statusData      = floor($build->coverage_globaldata->statementcoverage) . '%';
                    $this->statusDataLabel = '<br/>statements';
                    if ($build->coverage_globaldata->statementcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->statementcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }
                    break;

                default:
                    $this->statusData      = floor($build->coverage_globaldata->methodcoverage) . '%';
                    $this->statusDataLabel = '<br/>methods';
                    if ($build->coverage_globaldata->methodcoverage > 98) {
                        $this->status = 'ok';
                    } else if ($build->coverage_globaldata->methodcoverage > 95) {
                        $this->status = 'unstable';
                    } else {
                        $this->status = 'error';
                    }

                    $this->substatusData      = floor($build->coverage_globaldata->statementcoverage) . '%';
                    $this->substatusDataLabel = '<br/>statements';
                    if ($build->coverage_globaldata->statementcoverage > 98) {
                        $this->substatus = 'ok';
                    } else if ($build->coverage_globaldata->statementcoverage > 95) {
                        $this->substatus = 'unstable';
                    } else {
                        $this->substatus = 'error';
                    }

                    if ($this->status == 'ok' && $this->substatus == 'ok') {
                        $this->widgetStatus = 'ok';
                    } else {
                        $this->widgetStatus = 'unstable';
                    }
                    break;
            }
        }
    }
}