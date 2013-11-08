<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the number of errors and failures of the latest 50 builds.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpunit_Latestbuildssparklines extends Controller_Widget_Sparklines
{

    public static $icon  = 'check';
    public static $title = 'PHPUnit';

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
        );
    }

    /**
     * Processes the widget for main dashboard
     */
    public function display_main()
    {
        return $this->display_project();
    }

    /**
     * Processes the widget for project dashboard
     */
    public function display_project()
    {
        $builds = $this->getLastBuilds(50);
        $this->process($builds);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build[] $builds Builds to process, from latest to oldest
     */
    protected function process($builds)
    {
        if (sizeof($builds) > 0) {
            $this->widgetLinks[] = array(
                'type' => 'build',
                'id'   => $builds[0]->id
            );
            $this->widgetLinks[] = array(
                'title' => 'latest report',
                'url'   => Owaka::getReportUri($builds[0]->id, 'phpunit', 'report')
            );
        }

        $tests    = array();
        $failures = array();
        $errors   = array();

        foreach ($builds as $build) {
            if ($build->phpunit_globaldata->loaded()) {
                $tests[]    = $build->phpunit_globaldata->tests;
                $failures[] = $build->phpunit_globaldata->failures;
                $errors[]   = $build->phpunit_globaldata->errors;
            }
        }

        $this->sparklines[] = array('title' => 'Tests', 'data'  => array_reverse($tests));
        $this->sparklines[] = array('title' => 'Failures', 'data'  => array_reverse($failures));
        $this->sparklines[] = array('title' => 'Errors', 'data'  => array_reverse($errors));
    }
}