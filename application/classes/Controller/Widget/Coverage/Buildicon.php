<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the coverage status of a build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Coverage_Buildicon extends Controller_Widget_Icon
{

    public static $icon  = 'screenshot';
    public static $title = 'Coverage';

    /**
     * Gets the expected parameters
     * 
     * @param string $dashboard Type of dashboard
     * 
     * @return array
     */
    static public function expectedParameters($dashboard)
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
     * Processes the widget for all dashboards
     */
    public function display_all()
    {
        $build = $this->getBuild();
        if ($build === NULL) {
            $build = $this->getLastBuild();
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
        $data       = $build->coverage_globaldata;
        $parameters = Owaka::getReportParameters($build->project_id, 'coverage');
        $display    = $this->getParameter('display');

        $this->widgetLinks[] = array(
            'type' => 'build',
            'id'   => $build->id
        );
        $this->widgetLinks[] = array(
            'title' => 'report',
            'url'   => Owaka::getReportUri($build->id, 'coverage', 'dir')
        );

        if ($display == 'total') {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => floor($data->totalcoverage) . '%',
                'label'  => 'total'
            );
        }
        if ($display == 'methods' || $display == 'methods+statements') {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => floor($data->methodcoverage) . '%',
                'label'  => 'methods'
            );
        }
        if ($display == 'statements' || $display == 'methods+statements') {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => floor($data->statementcoverage) . '%',
                'label'  => 'statements'
            );
        }
    }
}
