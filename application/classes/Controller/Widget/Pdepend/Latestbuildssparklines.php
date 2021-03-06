<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays several metrics of the latest 50 builds.
 * 
 * Displays:
 * * Cyclomatic Cycle Number (ccn)
 * * Extended Cyclomatic Cycle Number (ccn2)
 * * Comment Lines of code (cloc)
 * * Executable lines of code (eloc)
 * * Logical lines of code (lloc)
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Pdepend_Latestbuildssparklines extends Controller_Widget_Sparklines
{

    public static $icon  = 'certificate';
    public static $title = 'PHP Depend';

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
                'url'   => Owaka::getReportUri($builds[0]->id, 'pdepend', 'summary')
            );
        }

        $ccn  = array();
        $ccn2 = array();
        $cloc = array();
        $eloc = array();
        $lloc = array();

        foreach ($builds as $build) {
            if ($build->pdepend_globaldata->loaded()) {
                $ccn[]  = $build->pdepend_globaldata->ccn;
                $ccn2[] = $build->pdepend_globaldata->ccn2;
                $cloc[] = $build->pdepend_globaldata->cloc;
                $eloc[] = $build->pdepend_globaldata->eloc;
                $lloc[] = $build->pdepend_globaldata->lloc;
            }
        }

        $this->sparklines[] = array('title' => 'Complexity', 'data'  => array_reverse($ccn));
        $this->sparklines[] = array('title' => 'Ext. Complexity', 'data'  => array_reverse($ccn2));
        $this->sparklines[] = array('title' => 'Comment Lines', 'data'  => array_reverse($cloc));
        $this->sparklines[] = array('title' => 'Executable Lines', 'data'  => array_reverse($eloc));
        $this->sparklines[] = array('title' => 'Logical Lines', 'data'  => array_reverse($lloc));
    }
}