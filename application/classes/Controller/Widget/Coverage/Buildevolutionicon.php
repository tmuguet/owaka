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
     * 
     * @param string $dashboard Type of dashboard
     * 
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
     * 
     * @return string
     */
    protected function getWidgetIcon()
    {
        return 'screenshot';
    }

    /**
     * Gets the widget title
     * 
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
                    ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                    ->with('coverage_globaldata')
                    ->find();
        }

        if ($build->coverage_globaldata->loaded()) {
            $this->process($build);
        }
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build Current build to process
     */
    protected function process(Model_Build &$build)
    {
        $data    = $build->coverage_globaldata;
        $display = $this->getParameter('display');

        $this->widgetLinks[] = array(
            "type" => 'build',
            "id"   => $build->id
        );
        $this->widgetLinks[] = array(
            "title" => 'report',
            "url"   => Owaka::getReportUri($build->id, 'coverage', 'dir')
        );

        if ($display == 'total') {
            if ($data->totalcoverage_delta < 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_ERROR,
                    'data'   => $data->totalcoverage_delta,
                    'label'  => 'total'
                );
            } else if ($data->totalcoverage_delta > 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '+' . $data->totalcoverage_delta,
                    'label'  => 'total'
                );
            } else {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '-',
                    'label'  => '<br>total'
                );
            }
        }
        if ($display == 'methods' || $display == 'methods+statements') {
            if ($data->methodcoverage_delta < 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_ERROR,
                    'data'   => $data->methodcoverage_delta,
                    'label'  => 'methods'
                );
            } else if ($data->methodcoverage_delta > 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '+' . $data->methodcoverage_delta,
                    'label'  => 'methods'
                );
            } else {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '-',
                    'label'  => '<br>methods'
                );
            }
        }
        if ($display == 'statements' || $display == 'methods+statements') {
            if ($data->statementcoverage_delta < 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_ERROR,
                    'data'   => $data->statementcoverage_delta,
                    'label'  => 'statements'
                );
            } else if ($data->statementcoverage_delta > 0) {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '+' . $data->statementcoverage_delta,
                    'label'  => 'statements'
                );
            } else {
                $this->data[] = array(
                    'status' => Owaka::BUILD_OK,
                    'data'   => '-',
                    'label'  => '<br>statements'
                );
            }
        }
    }
}
