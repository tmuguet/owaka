<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for switch to a branch in a project
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Switch extends Minion_Task
{

    // @codingStandardsIgnoreStart
    protected $_options = array(
        'id'      => NULL,
        'project' => NULL,
    );

    /**
     * Executes the task
     * 
     * @param array $params Parameters
     */
    protected function _execute(array $params)
    {
        try {
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
                    $log = $command->execute('hg pull');
                    $log .= $command->execute('hg update');
                    $log .= $command->execute('hg branch ' . $project->scm_branch, $res);
                    break;

                case 'git':
                    $log = $command->execute('git pull');
                    $log .= $command->execute('git checkout ' . $project->scm_branch, $res);
                    break;
            }
            if ($res != 0) {
                echo "Status $res\n" . $log;
                return;
            }

            $project->scm_status = 'ready';
            $project->update();
            echo 'ok';
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    // @codingStandardsIgnoreEnd
}