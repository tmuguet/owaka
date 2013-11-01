<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for checking out projects
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Checkout extends Minion_Task
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
                echo 'No project';
                return;
            }
            if ($project->scm_status != 'void') {
                echo 'Project has already been checked out';
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
                echo 'Status ' . $res . PHP_EOL . $log;
                return;
            }

            $project->scm_status = 'checkedout';
            $project->update();
            echo Owaka::BUILD_OK;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    // @codingStandardsIgnoreEnd
}