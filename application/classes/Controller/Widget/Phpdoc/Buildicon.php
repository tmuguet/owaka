<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the status of Phpdoc.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpdoc_Buildicon extends Controller_Widget_Icon
{

    public static $icon  = 'book';
    public static $title = 'phpdoc';

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

        $this->process($build);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build Current build to process
     */
    protected function process(Model_Build &$build)
    {
        $path = Owaka::getReportUri($build->id, 'phpdoc', 'report');
        if (!empty($path)) {
            $this->data[] = array(
                'status' => Owaka::BUILD_OK
            );

            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $build->id
            );
            $this->widgetLinks[] = array(
                'title' => 'report',
                'url'   => $path
            );
        } else {
            $this->widgetStatus = Owaka::BUILD_NODATA;
        }
    }
}
