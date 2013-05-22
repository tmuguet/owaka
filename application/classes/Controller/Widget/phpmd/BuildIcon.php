<?php

/**
 * Displays the number of errors of a build.
 */
class Controller_Widget_phpmd_BuildIcon extends Controller_Widget_BaseIcon
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
        return Owaka::ICON_FLAG;
    }

    /**
     * Gets the widget title
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

        $this->process($build);
    }

    /**
     * Processes the widget
     * @param Model_Build $build     Current build to process
     */
    protected function process(Model_Build &$build)
    {

        if (!$build->phpmd_globaldata->loaded()) {
            $this->status     = 'nodata';
            $this->statusData = 'No data';
        } else {
            $this->widgetLinks[] = array(
                "type" => 'build',
                "id"   => $build->id
            );
            $this->widgetLinks[] = array(
                "title" => 'report',
                "url"   => 'reports/' . $build->id . '/phpmd/index.html'
            );

            if ($build->phpmd_globaldata->errors == 0) {
                $this->status = 'ok';
            } else {
                $this->status          = 'unstable';
                $this->statusData      = $build->phpmd_globaldata->errors;
                $this->statusDataLabel = 'errors';
            }
        }
    }
}