<?php

class Task_Switch extends Minion_Task
{

    protected $_options = array(
        'id'      => NULL,
        'project' => NULL,
    );

    protected function _execute(array $params)
    {
        if (isset($params['project'])) {
            $project = $params['project'];
        } else {
            $project = ORM::factory('Project', $params['id']);
        }
        if (!$project->loaded()) {
            echo "No project";
            return;
        }
        if ($project->scm_status != 'checkedout') {
            echo "Project has not been checked out or has already been switched";
            return;
        }

        $command = new Command($project);
        $command->chdir($project->path);

        $res = '';
        switch ($project->scm) {
            case 'mercurial':
                $log = $command->execute('hg update ' . $project->scm_branch, $res);
                if ($res != 0) {
                    echo "Status $res\n" . $log;
                    return;
                }
                break;

            case 'git':
                $log = $command->execute('git checkout ' . $project->scm_branch, $res);
                if ($res != 0) {
                    echo "Status $res\n" . $log;
                    return;
                }
                break;
        }

        $project->scm_status = 'ready';
        $project->update();
        echo 'ok';
    }
}