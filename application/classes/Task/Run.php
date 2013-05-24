<?php

class Task_Run extends Minion_Task
{

    protected function _execute(array $params)
    {
        $build = ORM::factory('Build')
                ->where('status', '=', 'queued')
                ->order_by('started', 'ASC')
                ->order_by('id', 'ASC')
                ->limit(1)
                ->find();

        if (!$build->loaded()) {
            return;
        }

        $this->validate($build);
        $this->nightly($build);
        $this->parseReports($build);
        $this->analyseReports($build);
    }

    protected function validate(Model_Build &$build)
    {
        // Get last build duration to compute ETA
        $lastBuild    = $build->project->builds
                ->where('status', 'NOT IN', array('queued', 'building'))
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();
        $lastStart    = new DateTime($lastBuild->started);
        $lastFinished = new DateTime($lastBuild->finished);
        $lastDuration = $lastFinished->diff($lastStart, TRUE);

        chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

        $build->status  = 'building';
        $build->started = DB::expr('NOW()');
        $build->eta     = DB::expr('ADDTIME(NOW(), \'' . $lastDuration->format('%H:%I:%S') . '\')');
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
        foreach (File::findProcessors() as $processor) {
            $name = str_replace("Controller_", "", $processor);
            $request = Request::factory($name . '/copy/' . $build->id)
                    ->execute();
        }
    }

    protected function nightly(Model_Build &$build)
    {
        if (!empty($build->project->phing_target_nightly)) {
            chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

            passthru(
                    'phing -logger phing.listener.HtmlColorLogger -logfile nightly.html ' . $build->project->phing_target_nightly,
                    $updateResult
            );
            $outdir = APPPATH . '/reports/' . $build->id . '/';
            rename('nightly.html', $outdir . 'nightly.html');

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
        foreach (File::findProcessors() as $processor) {
            $name = str_replace("Controller_", "", $processor);
            $request = Request::factory($name . '/process/' . $build->id)
                    ->execute();
        }
    }

    protected function analyseReports(Model_Build &$build)
    {
        $build->status = 'ok';
        foreach (File::findAnalyzers() as $processor) {
            $name = str_replace("Controller_", "", $processor);
            $result = Request::factory($name . '/analyze/' . $build->id)
                    ->execute()->body();
            
            if ($result == 'error') {
                $build->status = 'error';
                break;
            } else if ($result == 'unstable') {
                $build->status = 'unstable';
            }
        }
        $build->finished = DB::expr('NOW()');
        $build->update();
    }
}