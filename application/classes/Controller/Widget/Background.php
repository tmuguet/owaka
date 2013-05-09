<?php

class Controller_Widget_Background extends Controller_Widget_Base
{

    private $_theme             = NULL;
    private $_status            = NULL;
    private $_additionalContent = NULL;

    static public function getPreferredSize()
    {
        return array(0, 0);
    }

    static public function getOptimizedSizes()
    {
        return array(array(0, 0));
    }

    static public function getExpectedParameters()
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

    public function display_build()
    {
        $this->_status = ($this->getBuild()->loaded() ? $this->getBuild()->status : 'ok');
        $this->_theme  = $this->getParameter('theme');
    }

    public function sample_all()
    {
        $this->_status = 'ok';
        $this->_theme  = $this->getParameter('theme');

        $view                     = View::factory('widgets/BaseIcon')
                ->set('status', $this->_status);
        $this->_additionalContent = $view;
    }
}