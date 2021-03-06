<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for queuing projects with new commits
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Queue extends Minion_Task
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
     * 
     * @SuppressWarnings("unused")
     */
    protected function _execute(array $params)
    {
        try {
            if (isset($params['project']) || isset($params['id'])) {
                $this->run($params);
            } else {
                $this->runAll();
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    // @codingStandardsIgnoreEnd

    /**
     * Finds a project to queue
     * 
     * @param array $params Parameters
     */
    protected function run(array $params)
    {
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
        $this->queue($project);
        echo Owaka::BUILD_OK;
    }

    /**
     * Finds all projects to queue
     */
    protected function runAll()
    {
        $ignore    = ORM::factory('Build')
                ->where('status', 'IN', array(Owaka::BUILD_BUILDING, Owaka::BUILD_QUEUED))
                ->find_all();
        $ignoreIds = array();
        foreach ($ignore as $i) {
            $ignoreIds[] = $i->project_id;
        }

        $todo = ORM::factory('Project')
                ->where('is_active', '=', 1)
                ->where('scm_status', '=', 'ready');
        if (!empty($ignoreIds)) {
            $todo->where('id', 'NOT IN', $ignoreIds);
        }
        $projects = $todo->find_all();

        foreach ($projects as $project) {
            $this->queue($project);
        }
        echo Owaka::BUILD_OK;
    }

    /**
     * Pulls and queues a project
     * 
     * @param Model_Project &$project Project
     */
    protected function queue(Model_Project &$project)
    {
        $command = new Command($project);
        $command->chdir($project->path);

        switch ($project->scm) {
            case 'mercurial':
                $log = $command->execute('hg pull');
                $log .= $command->execute('hg update');
                break;

            case 'git':
                $log = $command->execute('git pull');
                break;
        }

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

        if ($rev != $project->lastrevision) {
            $build             = ORM::factory('Build');
            $build->project_id = $project->id;
            $build->revision   = $rev;
            $build->message    = implode(PHP_EOL, $tip);
            $build->status     = Owaka::BUILD_QUEUED;
            $build->started    = DB::expr('NOW()');
            $build->eta        = NULL;
            $build->finished   = NULL;
            $build->create();
        }

        $command->chtobasedir();
    }
}
