<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the evolution of coverage with previous build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Coverage_Buildevolutionicon extends Controller_Widget_Icon
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
            'type' => 'build',
            'id'   => $build->id
        );
        $this->widgetLinks[] = array(
            'title' => 'report',
            'url'   => Owaka::getReportUri($build->id, 'coverage', 'dir')
        );

        if ($display == 'total') {
            $this->do_processTotal($data);
        }
        if ($display == 'methods' || $display == 'methods+statements') {
            $this->do_processMethods($data);
        }
        if ($display == 'statements' || $display == 'methods+statements') {
            $this->do_processStatements($data);
        }
    }

    /**
     * Processes the widget for "total" display
     * 
     * @param Model_Coverage_Globaldata &$data Coverage data
     */
    protected function do_processTotal(Model_Coverage_Globaldata &$data)
    {
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

    /**
     * Processes the widget for "methods" display
     * 
     * @param Model_Coverage_Globaldata &$data Coverage data
     */
    protected function do_processMethods(Model_Coverage_Globaldata &$data)
    {
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

    /**
     * Processes the widget for "statements" display
     * 
     * @param Model_Coverage_Globaldata &$data Coverage data
     */
    protected function do_processStatements(Model_Coverage_Globaldata &$data)
    {
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
