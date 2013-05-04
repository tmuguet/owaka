<?php

class Task_Refresh extends Minion_Task
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
                ->where('is_active', '=', 1);
        if (!empty($ignoreIds)) {
            $todo->where('id', 'NOT IN', $ignoreIds);
        }
        $projects = $todo->find_all();

        foreach ($projects as $project) {
            chdir($project->path);

            switch ($project->scm) {
                case 'mercurial':
                    if ($project->has_parent) {
                        passthru('hg pull', $result);
                    }
                    passthru('hg update', $result);
                    break;

                case 'git':
                    passthru('git update', $result);
                    break;
            }

            switch ($project->scm) {
                case 'mercurial':
                    exec('hg tip', $tip);
                    preg_match('/\s(\d+):/', $tip[0], $matches);
                    $rev = $matches[1];
                    break;

                case 'git':
                    exec('git log -1', $tip);
                    preg_match('/commit\s+([0-9a-f]+)/', $tip[0], $matches);
                    $rev = $matches[1];
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

            chdir(DOCROOT);
        }
    }
}