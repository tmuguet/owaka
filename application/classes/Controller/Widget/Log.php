<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Widget for display the latest build log file
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Log extends Controller_Widget_Raw
{

    public static $icon  = 'file-text-alt';
    public static $title = 'Log';

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

        $file = APPPATH . 'reports' . DIR_SEP . $build->id . DIR_SEP . 'owaka' . DIR_SEP . 'buildlog.html';
        if (!file_exists($file)) {
            $this->content = 'No data';
        } else {
            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $build->id
            );

            $this->content = file_get_contents($file);
        }
    }
}