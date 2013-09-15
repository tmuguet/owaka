<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the evolution of the number of errors with previous build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpmd_Buildevolutionicon extends Controller_Widget_Icon
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
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                    ->with('phpmd_globaldata')
                    ->find();
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
        $this->widgetLinks[] = array(
            'type' => 'build',
            'id'   => $build->id
        );
        $this->widgetLinks[] = array(
            'title' => 'report',
            'url'   => Owaka::getReportUri($build->id, 'phpmd', 'html')
        );

        if ($data->errors_delta > 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_ERROR,
                'data'   => '+' . $data->errors_delta,
                'label'  => 'errors'
            );
        } else if ($data->errors_delta < 0) {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK,
                'data'   => $data->errors_delta,
                'label'  => 'errors'
            );
        } else {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK,
                'data'   => '-',
                'label'  => '<br>no changes'
            );
        }
    }
}
