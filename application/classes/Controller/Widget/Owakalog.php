<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Widget for display the latest build owaka log file
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Owakalog extends Controller_Widget_Raw
{

    public static $icon  = 'file-text-alt';
    public static $title = 'Owaka log';

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
            $build = $this->getProject()->lastBuild()
                    ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                    ->find();
        }

        $file = APPPATH . 'reports' . DIR_SEP . $build->id . DIR_SEP . 'owaka' . DIR_SEP . 'builder.log';
        if (!file_exists($file)) {
            $this->content = 'No data';
        } else {
            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $build->id
            );

            $this->content = nl2br(file_get_contents($file));
        }
    }
}
