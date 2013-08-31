<?php

/**
 * Displays the coverage status of a build.
 * 
 * @package Widgets
 */
class Controller_Widget_Coverage_Buildicon extends Controller_Widget_Baseicon
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
                    ->where('status', 'NOT IN', array('building', 'queued'))
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
            if ($data->totalcoverage > 98) {
                $status = 'ok';
            } else if ($data->totalcoverage > 95) {
                $status = 'unstable';
            } else {
                $status = 'error';
            }

            $this->data[] = array(
                'status' => $status,
                'data'   => floor($data->totalcoverage) . '%',
                'label'  => 'total'
            );
        }
        if ($display == 'methods' || $display == 'methods+statements') {
            if ($data->methodcoverage > 98) {
                $status = 'ok';
            } else if ($data->methodcoverage > 95) {
                $status = 'unstable';
            } else {
                $status = 'error';
            }

            $this->data[] = array(
                'status' => $status,
                'data'   => floor($data->methodcoverage) . '%',
                'label'  => 'methods'
            );
        }
        if ($display == 'statements' || $display == 'methods+statements') {
            if ($data->statementcoverage > 98) {
                $status = 'ok';
            } else if ($data->statementcoverage > 95) {
                $status = 'unstable';
            } else {
                $status = 'error';
            }

            $this->data[] = array(
                'status' => $status,
                'data'   => floor($data->statementcoverage) . '%',
                'label'  => 'statements'
            );
        }
    }
}