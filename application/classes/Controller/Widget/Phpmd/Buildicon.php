<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the number of errors of a build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpmd_Buildicon extends Controller_Widget_Icon
{

    public static $icon  = 'flag-alt';
    public static $title = 'PHPMD';

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

        if ($build->phpmd_globaldata->loaded()) {
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
        $data                = $build->phpmd_globaldata;
        $parameters          = Owaka::getReportParameters($build->project_id, 'phpmd');
        $this->widgetLinks[] = array(
            'type' => 'build',
            'id'   => $build->id
        );
        $this->widgetLinks[] = array(
            'title' => 'report',
            'url'   => Owaka::getReportUri($build->id, 'phpmd', 'html')
        );

        if ($data->errors > 0) {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => $data->errors,
                'label'  => 'errors'
            );
        } else {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => '-'
            );
        }
    }
}
