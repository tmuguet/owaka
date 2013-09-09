<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the number of errors of a build.
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Phpmd_Buildicon extends Controller_Widget_Baseicon
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
                    ->where('status', 'NOT IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                    ->with('phpmd_globaldata')
                    ->find();
        }

        if ($build->phpmd_globaldata->loaded()) {
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
        $data                = $build->phpmd_globaldata;
        $parameters          = Owaka::getReportParameters($build->project_id, 'phpmd');
        $this->widgetLinks[] = array(
            "type" => 'build',
            "id"   => $build->id
        );
        $this->widgetLinks[] = array(
            "title" => 'report',
            "url"   => Owaka::getReportUri($build->id, 'phpmd', 'html')
        );

        if ($data->errors > 0) {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => $data->errors,
                'label'  => 'errors'
            );
        } else {
            $this->data[] = array(
                'status' => $data->buildStatus($parameters),
                'data'   => '-'
            );
        }
    }
}
