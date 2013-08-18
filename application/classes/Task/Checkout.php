<?php

class Task_Checkout extends Minion_Task
{

    protected $_options = array(
        'id' => NULL,
    );

    protected function _execute(array $params)
    {
        $project = ORM::factory('Project', $params['id']);
        if (!$project->loaded()) {
            echo "No project";
            return;
        }
        if ($project->is_ready) {
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
                if ($res != 0) {
                    echo "Error during cloning: status $res\n" . $log;
                    return;
                }
                $log = $command->execute('hg update ' . $project->scm_branch, $res);
                if ($res != 0) {
                    echo "Error during switching branch: status $res\n" . $log;
                    return;
                }
                break;

            case 'git':
                $log = $command->execute('git clone ' . $project->scm_url . ' ./', $res);
                if ($res != 0) {
                    echo "Error during cloning: status $res\n" . $log;
                    return;
                }
                $log = $command->execute('git checkout ' . $project->scm_branch, $res);
                if ($res != 0) {
                    echo "Error during switching branch: status $res\n" . $log;
                    return;
                }
                break;
        }

        $project->is_ready = 1;
        $project->update();
        echo 'ok';
    }
}