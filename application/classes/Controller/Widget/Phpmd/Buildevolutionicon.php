<?php

/**
 * Displays the evolution of the number of errors with previous build.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpmd_Buildevolutionicon extends Controller_Widget_Baseicon
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
            )
        );
    }

    /**
     * Gets the widget icon
     * 
     * @return string
     */
    protected function getWidgetIcon()
    {
        return 'flag-alt';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'PHPMD';
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
                    ->with('phpmd_globaldata')
                    ->find();
        }

        $prevBuild = $build->previousBuild()
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->with('phpmd_globaldata')
                ->find();

        $this->process($build, $prevBuild);
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build     Current build to process
     * @param Model_Build &$prevBuild Previous build to process
     */
    protected function process(Model_Build &$build, Model_Build &$prevBuild)
    {
        if (!$build->phpmd_globaldata->loaded() || !$prevBuild->phpmd_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => Owaka::getReportUri($build->id, 'phpmd', 'html')
            );

            $errors = $build->phpmd_globaldata->errors - $prevBuild->phpmd_globaldata->errors;

            if ($errors == 0) {
                $this->status          = 'ok';
                $this->statusData      = '-';
                $this->statusDataLabel = '<br>no changes';
            } else {
                $this->status          = ($errors > 0 ? 'error' : 'ok');
                $this->statusData      = ($errors > 0 ? '+' . $errors : $errors);
                $this->statusDataLabel = 'errors';
            }
        }
    }
}