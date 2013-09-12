<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for queuing a project
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Forcequeue extends Minion_Task
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
            if ($project->scm_status != 'ready') {
                echo 'Project has not been checked out';
                return;
            }
            $building = $project->builds->where('status', 'IN', array('building', 'queued'))->count_all();
            if ($building > 0) {
                echo 'Project already in queue';
                return;
            }

            $command = new Command($project);
            $command->chdir($project->path);

            switch ($project->scm) {
                case 'mercurial':
                    $tip_res = $command->execute('hg tip');
                    $tip     = explode(PHP_EOL, $tip_res);
                    preg_match('/\s(\d+):/', $tip[0], $matches);
                    $rev     = $matches[1];
                    break;

                case 'git':
                    $tip_res = $command->execute('git log -1');
                    $tip     = explode(PHP_EOL, $tip_res);
                    preg_match('/commit\s+([0-9a-f]+)/', $tip[0], $matches);
                    $rev     = $matches[1];
                    break;
            }

            $build             = ORM::factory('Build');
            $build->project_id = $project->id;
            $build->revision   = $rev;
            $build->message    = implode(PHP_EOL, $tip);
            $build->status     = Owaka::BUILD_QUEUED;
            $build->started    = DB::expr('NOW()');
            $build->eta        = NULL;
            $build->finished   = NULL;
            $build->create();

            $command->chtobasedir();
            echo 'ok';
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    // @codingStandardsIgnoreEnd
}
