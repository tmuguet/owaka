<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Widget for displaying a background image depending on the build status
 * 
 * @package   Widgets
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Controller_Widget_Background extends Controller_Widget
{

    public static $icon           = 'picture';
    public static $title          = 'background';
    public static $preferredSize  = array(1, 1);
    public static $availableSizes = array(array(1, 1));
    protected static $autorefresh    = TRUE;

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
     * Gets the expected parameters
     * 
     * @param string $dashboard Type of dashboard
     * 
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    static public function expectedParameters($dashboard)
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
     * Renders the background
     */
    protected function render()
    {
        parent::initViews();

        $content = <<<EOT
<script type="text/javascript">
    \$(document).ready(function() {
        \$("body").attr("class", "{$this->_theme} build-{$this->_status}");
    });
</script>
EOT;
        if (!empty($this->_additionalContent)) {
            $content .= $this->_additionalContent;
        }

        $this->success($content);
    }

    /**
     * Displays for main dashboard.
     * 
     * Fetches the latest builds of each projects; widget status is the more severe status found.
     */
    public function display_main()
    {
        $projects = ORM::factory('Project')
                ->where('is_active', '=', '1')
                ->find_all();

        $this->_status = Owaka::BUILD_OK;

        foreach ($projects as $project) {
            $build = $project->builds
                    ->where('status', 'IN', array(Owaka::BUILD_OK, Owaka::BUILD_UNSTABLE, Owaka::BUILD_ERROR))
                    ->order_by('id', 'DESC')
                    ->limit(1)
                    ->find();

            if ($build->loaded()) {
                if ($build->status == Owaka::BUILD_ERROR) {
                    $this->_status = Owaka::BUILD_ERROR;
                    break;
                } else if ($build->status == Owaka::BUILD_UNSTABLE) {
                    $this->_status = Owaka::BUILD_UNSTABLE;
                }
            }
        }

        $this->_theme = $this->getParameter('theme');
    }

    /**
     * Displays for project dashboard.
     * 
     * Widget status is the status of latest build.
     */
    public function display_project()
    {
        $build = $this->getLastBuild();

        $this->_status = ($build->loaded() ? $build->status : Owaka::BUILD_OK);
        $this->_theme  = $this->getParameter('theme');
    }

    /**
     * Displays for project build.
     * 
     * Widget status is the status of the build.
     */
    public function display_build()
    {
        $this->_status = ($this->getBuild()->loaded() ? $this->getBuild()->status : Owaka::BUILD_OK);
        $this->_theme  = $this->getParameter('theme');
    }

    /**
     * Displays a sample for main dashboard.
     */
    public function sample_main()
    {
        $this->display_main();

        $this->_additionalContent = View::factory('widgets' . DIR_SEP . 'BaseIcon')
                ->set('data', array());
    }

    /**
     * Displays a sample for project dashboard.
     */
    public function sample_project()
    {
        $this->display_project();

        $this->_additionalContent = View::factory('widgets' . DIR_SEP . 'BaseIcon')
                ->set('data', array());
    }

    /**
     * Displays a sample for build dashboard.
     */
    public function sample_build()
    {
        $this->display_build();

        $this->_additionalContent = View::factory('widgets' . DIR_SEP . 'BaseIcon')
                ->set('data', array());
    }
}
