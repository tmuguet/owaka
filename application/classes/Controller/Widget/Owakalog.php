<?php

/**
 * Widget for display the latest build owaka log file
 * 
 * @package Widgets
 */
class Controller_Widget_Owakalog extends Controller_Widget_Baseraw
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
        return Owaka::ICON_DOC;
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Owaka Log';
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

        $file = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $build->id . DIRECTORY_SEPARATOR . 'owaka' . DIRECTORY_SEPARATOR . 'builder.log';
        if (!file_exists($file)) {
            $this->content = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );

            $this->content = nl2br(file_get_contents($file));
        }
    }
}
