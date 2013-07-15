<?php

class Task_ForceQueue extends Minion_Task
{

    protected $_options = array(
        'id' => NULL,
    );

    public function build_validation(Validation $validation)
    {
        return parent::build_validation($validation)
                        ->rule('id', 'Model_Project::exists');
    }

    protected function _execute(array $params)
    {
        $project = ORM::factory('Project', $params['id']);
        if (!$project->loaded()) {
            echo "No project";
            return;
        }

        chdir($project->path);

        switch ($project->scm) {
            case 'mercurial':
                $tip = array();
                exec('hg tip', $tip);
                preg_match('/\s(\d+):/', $tip[0], $matches);
                $rev = $matches[1];
                break;

            case 'git':
                $tip = array();
                exec('git log -1', $tip);
                preg_match('/commit\s+([0-9a-f]+)/', $tip[0], $matches);
                $rev = $matches[1];
                break;
        }

        $build             = ORM::factory('Build');
        $build->project_id = $project->id;
        $build->revision   = $rev;
        $build->message    = implode("\n", $tip);
        $build->status     = "queued";
        $build->started    = DB::expr('NOW()');
        $build->eta        = NULL;
        $build->finished   = NULL;
        $build->create();

        chdir(DOCROOT);
    }
}