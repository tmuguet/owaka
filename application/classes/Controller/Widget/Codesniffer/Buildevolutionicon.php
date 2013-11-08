<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the evolution of the number of errors and warnings with previous build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Codesniffer_Buildevolutionicon extends Controller_Widget_Icon
{

    public static $icon  = 'shield';
    public static $title = 'Code sniffer';

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
            )
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

        if ($build->codesniffer_globaldata->loaded()) {
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
        $data                = $build->codesniffer_globaldata;
        $this->widgetLinks[] = array(
            'type' => 'build',
            'id'   => $build->id
        );
        $this->widgetLinks[] = array(
            'title' => 'report',
            'url'   => Owaka::getReportUri($build->id, 'codesniffer', 'xml')
        );

        if ($data->errors_regressions > 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_ERROR,
                'data'   => '+' . $data->errors_regressions,
                'label'  => 'rules errors'
            );
        } else if ($data->errors_fixed > 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK,
                'data'   => '-' . $data->errors_fixed,
                'label'  => 'rules errors'
            );
        }
        if ($data->warnings_regressions > 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_UNSTABLE,
                'data'   => '+' . $data->warnings_regressions,
                'label'  => 'rules warnings'
            );
        } else if ($data->warnings_fixed > 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK,
                'data'   => '-' . $data->warnings_fixed,
                'label'  => 'rules warnings'
            );
        }

        if (sizeof($this->data) == 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK,
                'data'   => '-',
                'label'  => '<br>no changes'
            );
        }
    }
}
