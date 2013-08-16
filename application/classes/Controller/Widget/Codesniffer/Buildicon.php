<?php

/**
 * Displays the number of errors and warnings of a build.
 * 
 * @package Widgets
 */
class Controller_Widget_Codesniffer_Buildicon extends Controller_Widget_Baseicon
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
        return 'shield';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Codesniffer';
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
                    ->with('codesniffer_globaldata')
                    ->find();
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
        if (!$build->codesniffer_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => Owaka::getReportUri($build->id, 'codesniffer', 'xml')
            );

            if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors == 0) {
                $this->status = 'ok';
            } else if ($build->codesniffer_globaldata->warnings > 0 && $build->codesniffer_globaldata->errors == 0) {
                $this->status          = 'unstable';
                $this->statusData      = $build->codesniffer_globaldata->warnings;
                $this->statusDataLabel = 'rules warnings';
            } else if ($build->codesniffer_globaldata->warnings == 0 && $build->codesniffer_globaldata->errors > 0) {
                $this->status          = 'error';
                $this->statusData      = $build->codesniffer_globaldata->errors;
                $this->statusDataLabel = 'rules errors';
            } else {
                $this->widgetStatus       = 'error';
                $this->status             = 'error';
                $this->statusData         = $build->codesniffer_globaldata->errors;
                $this->statusDataLabel    = 'errors';
                $this->substatus          = 'unstable';
                $this->substatusData      = $build->codesniffer_globaldata->warnings;
                $this->substatusDataLabel = 'warnings';
            }
        }
    }
}