<?php

class Controller_Widget_Background extends Controller_Widget_Base
{

    private $_theme  = NULL;
    private $_status = NULL;

    protected function render()
    {
        $content = <<<EOT
<script type="text/javascript">
    \$(document).ready(function() {
        \$("body").addClass("{$this->_theme} build-{$this->_status}");
    });
</script>
EOT;
        $this->response->body($content);
    }

    public function action_main()
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

        $params       = $this->getParameters();
        $this->_theme = $params['theme'];

        $this->render();
    }

    public function action_project()
    {
        $build = $this->getProject()->builds
                ->where('status', 'NOT IN', array('building', 'queued'))
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();

        $this->_status = $build->status;

        $params       = $this->getParameters();
        $this->_theme = $params['theme'];

        $this->render();
    }

    public function action_build()
    {
        $this->_status = $this->getBuild()->status;

        $params       = $this->getParameters();
        $this->_theme = $params['theme'];

        $this->render();
    }
}