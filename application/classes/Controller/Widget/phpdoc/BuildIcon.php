<?php

/**
 * Displays the status of Phpdoc.
 * 
 * @package Widgets
 */
class Controller_Widget_phpdoc_BuildIcon extends Controller_Widget_BaseIcon
{

    /**
     * Gets the expected parameters
     * @param string $dashboard Type of dashboard
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
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_BOOK4;
    }

    /**
     * Gets the widget title
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Phpdoc';
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
                    ->find();
        }

        $this->process($build);
    }

    /**
     * Processes the widget
     * @param Model_Build $build     Current build to process
     */
    protected function process(Model_Build &$build)
    {
        $path = Owaka::getReportUri($build->id, 'phpdoc', 'report');
        if (!empty($path)) {
            $this->status = 'ok';
            
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => $path
            );
        } else {
            $this->status     = 'nodata';
        }
    }
}