<?php

/**
 * Displays the evolution of coverage with previous build.
 * 
 * @package Widgets
 */
class Controller_Widget_Coverage_Buildevolutionicon extends Controller_Widget_Baseicon
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

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('coverage_globaldata')
                ->find();

        $this->process($build, $prevBuild);
    }

    /**
     * Processes the widget
     * @param Model_Build $build     Current build to process
     * @param Model_Build $prevBuild Previous build to process
     */
    protected function process(Model_Build &$build, Model_Build &$prevBuild)
    {
        if (!$build->coverage_globaldata->loaded() || !$prevBuild->coverage_globaldata->loaded()) {
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

            $total      = round(
                    $build->coverage_globaldata->totalcoverage - $prevBuild->coverage_globaldata->totalcoverage, 2
            );
            $methods    = round(
                    $build->coverage_globaldata->methodcoverage - $prevBuild->coverage_globaldata->methodcoverage, 2
            );
            $statements = round(
                    $build->coverage_globaldata->statementcoverage - $prevBuild->coverage_globaldata->statementcoverage,
                    2
            );

            switch ($display) {
                case 'total':
                    if ($total == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->widgetStatus    = ($total > 0 ? 'ok' : 'unstable');
                        $this->status          = $this->widgetStatus;
                        $this->statusData      = ($total > 0 ? '+' . $total : $total) . '%';
                        $this->statusDataLabel = '<br>total';
                    }
                    break;

                case 'methods':
                    if ($methods == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->widgetStatus    = ($methods > 0 ? 'ok' : 'unstable');
                        $this->status          = $this->widgetStatus;
                        $this->statusData      = ($methods > 0 ? '+' . $methods : $methods) . '%';
                        $this->statusDataLabel = '<br>methods';
                    }
                    break;

                case 'statements':
                    if ($statements == 0) {
                        $this->widgetStatus    = 'ok';
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->status          = ($statements > 0 ? 'ok' : 'unstable');
                        $this->statusData      = ($statements > 0 ? '+' . $statements : $statements) . '%';
                        $this->statusDataLabel = '<br>statements';
                    }
                    break;

                default:
                    if ($methods >= 0 && $statements >= 0) {
                        $this->widgetStatus = 'ok';
                    } else {
                        $this->widgetStatus = 'unstable';
                    }
                    if ($methods == 0) {
                        $this->status          = 'ok';
                        $this->statusData      = '-';
                        $this->statusDataLabel = '<br>no changes';
                    } else {
                        $this->status          = ($methods > 0 ? 'ok' : 'unstable');
                        $this->statusData      = ($methods > 0 ? '+' . $methods : $methods) . '%';
                        $this->statusDataLabel = '<br>methods';
                    }

                    if ($statements == 0) {
                        $this->substatus          = 'ok';
                        $this->substatusData      = '-';
                        $this->substatusDataLabel = '<br>no changes';
                    } else {
                        $this->substatus          = ($statements > 0 ? 'ok' : 'unstable');
                        $this->substatusData      = ($statements > 0 ? '+' . $statements : $statements) . '%';
                        $this->substatusDataLabel = '<br>statements';
                    }
                    break;
            }
        }
    }
}