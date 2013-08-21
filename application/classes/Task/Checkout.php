<?php

class Task_Checkout extends Minion_Task
{

    // @codingStandardsIgnoreStart
    protected $_options = array(
        'id'      => NULL,
        'project' => NULL,
    );

    protected function _execute(array $params)
    {
        // @codingStandardsIgnoreEnd
        if (isset($params['project'])) {
            $project = $params['project'];
        } else {
            $project = ORM::factory('Project', $params['id']);
        }
        if (!$project->loaded()) {
            echo "No project";
            return;
        }
        if ($project->scm_status != 'void') {
            echo "Project has already been checked out";
            return;
        }

        $command = new Command($project);
        if (!$command->is_dir($project->path)) {
            $command->mkdir($project->path, 0700, true);
        }
        $command->chdir($project->path);

        $res = '';
        switch ($project->scm) {
            case 'mercurial':
                $log = $command->execute('hg clone ' . $project->scm_url . ' ./', $res);
                break;

            case 'git':
                $log = $command->execute('git clone ' . $project->scm_url . ' ./', $res);
                break;
        }
        if ($res != 0) {
            echo "Status $res\n" . $log;
            return;
        }

        $project->scm_status = 'checkedout';
        $project->update();
        echo 'ok';
    }
}