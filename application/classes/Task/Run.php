<?php

class Task_Run extends Minion_Task
{

    protected function _execute(array $params)
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

    protected function validate(Model_Build &$build)
    {
        // Get last build duration to compute ETA
        $lastBuild = $build->project->builds
                ->where('status', 'NOT IN', array('queued', 'building'))
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();
        $lastStart = new DateTime($lastBuild->started);
        $lastFinished = new DateTime($lastBuild->finished);
        $lastDuration = $lastFinished->diff($lastStart, TRUE);
        
        $start = new DateTime();
        $eta = new DateTime();
        $eta->add($lastDuration);
        
        chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

        $build->status = 'building';
        $build->started = Date::toMySql($eta);
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
            $path = Owaka::getReportsPath($build->id, 'phpunit_dir');
            exec('cp -R ' . $path . ' ' . APPPATH . '/reports/' . $build->id . '/phpunit');
        }

        if (!empty($build->project->coverage_dir_report)) {
            $path = Owaka::getReportsPath($build->id, 'coverage_dir');
            exec('cp -R ' . $path . ' ' . APPPATH . '/reports/' . $build->id . '/coverage');
        }

        if (!empty($build->project->phpdoc_dir_report)) {
            $path = Owaka::getReportsPath($build->id, 'phpdoc_dir');
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
        $datas = array("codesniffer", "coverage", "pdepend", "phpmd", "unittest");
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
        $build->finished = DB::expr('NOW()');
        $build->update();
    }
}