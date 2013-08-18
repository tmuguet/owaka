<?php

class Task_Queue extends Minion_Task
{

    protected function _execute(array $params)
    {
        $ignore    = ORM::factory('Build')
                ->where('status', 'IN', array('building', 'queued'))
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
            $command = new Command($project);
            $command->chdir($project->path);

            switch ($project->scm) {
                case 'mercurial':
                    echo $command->execute('hg pull');
                    echo $command->execute('hg update');
                    break;

                case 'git':
                    echo $command->execute('git pull');
                    break;
            }

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

            if ($rev != $project->lastrevision) {
                $build             = ORM::factory('Build');
                $build->project_id = $project->id;
                $build->revision   = $rev;
                $build->message    = implode("\n", $tip);
                $build->status     = "queued";
                $build->started    = DB::expr('NOW()');
                $build->eta        = NULL;
                $build->finished   = NULL;
                $build->create();
            }

            $command->chtobasedir();
        }
    }
}