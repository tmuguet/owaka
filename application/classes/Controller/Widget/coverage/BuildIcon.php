<?php

/**
 * Displays the coverage status of a build.
 */
class Controller_Widget_coverage_BuildIcon extends Controller_Widget_BaseIcon
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

    /**
     * Gets the widget icon
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_TARGET;
    }

    /**
     * Gets the widget title
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Coverage';
    }

    /**
     * Processes the widget for all dashboards
     */
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

    /**
     * Processes the widget
     * @param Model_Build $build     Current build to process
     */
    protected function process(Model_Build &$build)
    {
        if (!$build->coverage_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $display = $this->getParameter('display');

            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => Owaka::getReportUri($build->id, 'coverage', 'dir')
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