<?php

/**
 * Displays the evolution of the number of errors and failures with previous build.
 * 
 * @package Widgets
 */
class Controller_Widget_Phpunit_Buildevolutionicon extends Controller_Widget_Baseicon
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
        return 'check';
    }

    /**
     * Gets the widget title
     * 
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'phpunit';
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
                    ->with('phpunit_globaldata')
                    ->find();
        }

        if ($build->phpunit_globaldata->loaded()) {
            $this->process($build);
        }
    }

    /**
     * Processes the widget
     * 
     * @param Model_Build &$build Current build to process
     */
    protected function process(Model_Build &$build)
    {
        $this->widgetLinks[] = array(
            "type" => 'build',
            "id"   => $build->id
        );
        $this->widgetLinks[] = array(
            "title" => 'report',
            "url"   => Owaka::getReportUri($build->id, 'phpunit', 'report')
        );

        if ($data->errors_regressions > 0) {
            $this->data[] = array(
                'status' => 'error',
                'data'   => '+' . $data->errors_regressions,
                'label'  => 'errors'
            );
        } else if ($data->errors_fixed > 0) {
            $this->data[] = array(
                'status' => 'ok',
                'data'   => '-' . $data->errors_fixed,
                'label'  => 'errors'
            );
        }
        if ($data->failures_regressions > 0) {
            $this->data[] = array(
                'status' => 'unstable',
                'data'   => '+' . $data->failures_regressions,
                'label'  => 'failures'
            );
        } else if ($data->failures_fixed > 0) {
            $this->data[] = array(
                'status' => 'ok',
                'data'   => '-' . $data->failures_fixed,
                'label'  => 'failures'
            );
        }

        if (sizeof($this->data) == 0) {
            $this->data[] = array(
                'status' => 'ok',
                'data'   => '-',
                'label'  => '<br>no changes'
            );
        }
    }
}
