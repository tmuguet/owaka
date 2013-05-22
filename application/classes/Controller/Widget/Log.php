<?php

/**
 * Widget for display the latest build log file
 */
class Controller_Widget_Log extends Controller_Widget_BaseRaw
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
            )
        );
    }

    /**
     * Gets the widget icon
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_DOC;
    }

    /**
     * Gets the widget title
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'Log';
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

        if (!file_exists(APPPATH . 'reports/' . $build->id . '/log.html')) {
            $this->content = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );

            $this->content = file_get_contents(APPPATH . 'reports/' . $build->id . '/log.html');
        }
    }
}