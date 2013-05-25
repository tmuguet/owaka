<?php

/**
 * Widget for displaying a background image depending on the build status
 */
class Controller_Widget_Background extends Controller_Widget_Base
{

    /**
     * Theme of backgrounds
     * @var string
     */
    private $_theme = NULL;

    /**
     * Status
     * @var string 
     */
    private $_status = NULL;

    /**
     * Additional content (used in sample mode to actually display the widget on the grid)
     * @var string 
     */
    private $_additionalContent = NULL;

    /**
     * Gets the preferred size (width, height)
     * @return int[]
     */
    static public function getPreferredSize()
    {
        return array(0, 0);
    }

    /**
     * Gets the sizes (width, height) which this widget is optimized for
     * @return int[][]
     */
    static public function getOptimizedSizes()
    {
        return array(array(0, 0));
    }

    /**
     * Gets the expected parameters
     * @param string $dashboard Type of dashboard
     * @return array
     */
    static public function getExpectedParameters($dashboard)
    {
        return array(
            'theme' => array(
                'title'    => 'Theme',
                'type'     => 'enum',
                'enum'     => array('grunge', 'splotchy'),
                'default'  => 'grunge',
                'required' => false
            )
        );
    }

    /**
     * Gets the widget icon
     * @return string
     */
    protected function getWidgetIcon()
    {
        return Owaka::ICON_PIC;
    }

    /**
     * Gets the widget title
     * @return string
     */
    protected function getWidgetTitle()
    {
        return 'background';
    }

    /**
     * Renders the background
     */
    protected function render()
    {
        parent::initViews();

        $content = <<<EOT
<script type="text/javascript">
    \$(document).ready(function() {
        \$("body").addClass("{$this->_theme} build-{$this->_status}");
    });
</script>
EOT;
        if (!empty($this->_additionalContent)) {
            $content .= $this->_additionalContent;
        }

        $this->response->body($content);
    }

    /**
     * Displays for main dashboard.
     * Fetches the latest builds of each projects; widget status is the more severe status found.
     */
    public function display_main()
    {
        $projects = ORM::factory('Project')
                ->where('is_active', '=', '1')
                ->find_all();

        $this->_status = 'ok';

        foreach ($projects as $project) {
            $build = $project->builds
                    ->order_by('id', 'DESC')
                    ->limit(1)
                    ->find();

            if ($build->loaded()) {
                if ($build->status == 'error') {
                    $this->_status = 'error';
                    break;
                } else if ($build->status == 'unstable') {
                    $this->_status = 'unstable';
                }
            }
        }

        $this->_theme = $this->getParameter('theme');
    }

    /**
     * Displays for project dashboard.
     * Widget status is the status of latest build.
     */
    public function display_project()
    {
        $build = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();

        $this->_status = ($build->loaded() ? $build->status : 'ok');
        $this->_theme  = $this->getParameter('theme');
    }

    /**
     * Displays for project build.
     * Widget status is the status of the build.
     */
    public function display_build()
    {
        $this->_status = ($this->getBuild()->loaded() ? $this->getBuild()->status : 'ok');
        $this->_theme  = $this->getParameter('theme');
    }

    /**
     * Displays a sample for main dashboard.
     */
    public function sample_main()
    {
        $this->display_main();

        $this->_additionalContent = View::factory('widgets' . DIRECTORY_SEPARATOR . 'BaseIcon')
                ->set('status', $this->_status);
    }

    /**
     * Displays a sample for project dashboard.
     */
    public function sample_project()
    {
        $this->display_project();

        $this->_additionalContent = View::factory('widgets ' . DIRECTORY_SEPARATOR . 'BaseIcon')
                ->set('status', $this->_status);
    }

    /**
     * Displays a sample for build dashboard.
     */
    public function sample_build()
    {
        $this->display_build();

        $this->_additionalContent = View::factory('widgets' . DIRECTORY_SEPARATOR . 'BaseIcon')
                ->set('status', $this->_status);
    }
}