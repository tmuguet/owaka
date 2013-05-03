<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Tasks extends Controller_Data_Base // TODO: change this !
{

    public function action_refresh()
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
                    var_dump(exec('hg update', $result));
                    var_dump($result);
                    //passthru('hg update', $result);
                    break;

                case 'git':
                    passthru('git update', $result);
                    break;
            }
            
            switch ($project->scm) {
                case 'mercurial':
                    exec('hg tip', $tip);
                    var_dump($tip);
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
                $build->message    = explode("\n", $tip);
                $build->status     = "queued";
                $build->regression = 0;
                $build->started    = time();
                $build->eta        = NULL;
                $build->finished   = NULL;
                $build->create();
            }

            chdir(DOCROOT);
        }
    }

    protected function validate(Model_Build &$build)
    {
        chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

        $build->status = 'building';
        $build->update();

        passthru(
                'phing -logger phing.listener.HtmlColorLogger -logfile log.html ' . $build->project->phing_target_validate . ' -Dowaka.build=' . $build->id,
                $buildResult
        );

        $outdir = APPPATH . '/reports/' . $build->id . '/';
        mkdir($outdir, 0700);
        rename('log.html', $outdir . 'log.html');

        chdir(DOCROOT);

        $this->copyReports($build);

        if ($buildResult == 0) {
//$build->status = 'ok';    // Do not update yet
        } else if ($buildResult == 1) {
            $build->status = 'error';
        } else {
            $build->status = 'error';   // Build unproperly configured
        }

        $build->project->lastrevision = $build->revision;
        $build->project->update();
    }

    protected function copyReports(Model_Build &$build)
    {
        if (!empty($build->project->phpunit_dir_report)) {
            $path = $this->getReportsPath($build->id, 'phpunit_dir');
            exec('cp -R ' . $path . ' ' . APPPATH . '/reports/' . $build->id . '/phpunit');
        }

        if (!empty($build->project->coverage_dir_report)) {
            $path = $this->getReportsPath($build->id, 'coverage_dir');
            exec('cp -R ' . $path . ' ' . APPPATH . '/reports/' . $build->id . '/coverage');
        }

        if (!empty($build->project->phpdoc_dir_report)) {
            $path = $this->getReportsPath($build->id, 'phpdoc_dir');
            exec('cp -R ' . $path . ' ' . APPPATH . '/reports/' . $build->id . '/phpdoc');
        }
    }

    protected function nightly(Model_Build &$build)
    {
        if (!empty($build->project->phing_target_nightly)) {
            chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

            passthru(
                    'phing -logger phing.listener.HtmlColorLogger ' . $build->project->phing_target_nightly,
                    $updateResult
            );

            if ($updateResult == 0) {
                echo "Update successful\n";
            } else if ($updateResult == 1) {
                echo "Update failed with errors\n";
            } else {
                echo "Update unproperly configured\n";
            }
        }
    }

    protected function parseReports(Model_Build &$build)
    {
        $datas = array("unittest");
        foreach ($datas as $data) {
            $request = Request::factory('data_' . $data . '/parse/' . $build->id)
                    ->execute();
        }
    }

    protected function analyseReports(Model_Build &$build)
    {
        if ($build->phpunit_globaldata->failures == 0 && $build->phpunit_globaldata->errors == 0) {
            $build->status     = 'ok';
            $build->regression = 0;
        } else {
            $build->status = 'unstable';
// Get previous build and compare failures
        }
        $build->finished = time();
        $build->update();
    }

    public function action_validate()
    {
        $build = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->order_by('started', 'ASC')
                ->limit(1)
                ->find();

        $this->validate($build);
        $this->nightly($build);
        $this->parseReports($build);
        $this->analyseReports($build);
    }
}