<?php

/**
 * Task for queuing a project
 * 
 * @package Task
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
        if (isset($params['project'])) {
            $project = $params['project'];
        } else {
            $project = ORM::factory('Project', $params['id']);
        }
        if (!$project->loaded()) {
            echo "No project";
            return;
        }
        if ($project->scm_status != 'ready') {
            echo "Project has not been checked out";
            return;
        }
        $building = $project->builds->where('status', 'IN', array('building', 'queued'))->count_all();
        if ($building > 0) {
            echo "Project already in queue";
            return;
        }

        $command = new Command($project);
        $command->chdir($project->path);

        switch ($project->scm) {
            case 'mercurial':
                $tip_res = $command->execute('hg tip');
                $tip     = explode("\n", $tip_res);
                preg_match('/\s(\d+):/', $tip[0], $matches);
                $rev     = $matches[1];
                break;

            case 'git':
                $tip_res = $command->execute('git log -1');
                $tip     = explode("\n", $tip_res);
                preg_match('/commit\s+([0-9a-f]+)/', $tip[0], $matches);
                $rev     = $matches[1];
                break;
        }

        $build             = ORM::factory('Build');
        $build->project_id = $project->id;
        $build->revision   = $rev;
        $build->message    = implode("\n", $tip);
        $build->status     = Owaka::BUILD_QUEUED;
        $build->started    = DB::expr('NOW()');
        $build->eta        = NULL;
        $build->finished   = NULL;
        $build->create();

        $command->chtobasedir();
        echo 'ok';
    }
    // @codingStandardsIgnoreEnd
}
