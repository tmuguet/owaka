<?php

class Task_Run extends Minion_Task
{

    private $_outdir       = NULL;
    private $_outdir_owaka = NULL;

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

        $this->_outdir       = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $build->id . DIRECTORY_SEPARATOR;
        $this->_outdir_owaka = $this->_outdir . 'owaka' . DIRECTORY_SEPARATOR;

        mkdir($this->_outdir, 0700);
        mkdir($this->_outdir_owaka, 0700);
        $logger = new Log_FlatFile($this->_outdir_owaka . 'builder.log');
        Kohana::$log->attach($logger);

        Kohana::$log->add(Log::INFO, "Starting build " . $build->id . " for project " . $build->project->name);

        try {
            $this->validate($build);
            $this->nightly($build);
            $this->parseReports($build);
            $this->analyzeReports($build);
        } catch (Exception $e) {
            Kohana_Exception::log($e);
        }

        Kohana::$log->add(Log::INFO, "Finished build " . $build->id);
        Kohana::$log->write();
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

        Kohana::$log->add(Log::INFO, "Starting validating...");
        $buildLog = array();
        exec(
                'phing -logger phing.listener.HtmlColorLogger -logfile buildlog.html ' . $build->project->phing_target_validate . ' -Dowaka.build=' . $build->id,
                $buildLog, $buildResult
        );
        if (!empty($buildLog)) {
            Kohana::$log->add(Log::INFO, "Additional log: " . implode("\n", $buildLog));
        }
        Kohana::$log->add(Log::INFO, "Finished validating with result $buildResult");

        rename('buildlog.html', $this->_outdir_owaka . 'buildlog.html');

        chdir(DOCROOT);

        $this->copyReports($build);

        if ($buildResult == 0) {
            Kohana::$log->add(Log::INFO, "Build successful");
            //$build->status = 'ok';    // Do not update yet
        } else if ($buildResult == 1) {
            Kohana::$log->add(Log::INFO, "Build failed with errors");
            $build->status = 'error';
        } else {
            Kohana::$log->add(Log::INFO, "Build unproperly configured");
            $build->status = 'error';   // Build unproperly configured
        }

        $build->project->lastrevision = $build->revision;
        $build->project->update();
    }

    protected function copyReports(Model_Build &$build)
    {
        Auth::instance()->force_login('owaka');
        foreach (File::findProcessors() as $processor) {
            $name     = str_replace("Controller_", "", $processor);
            Kohana::$log->add(Log::INFO, "Copying reports for $name...");
            $response = Request::factory($name . '/copy/' . $build->id)
                    ->execute();
            if ($response->status() != 200) {
                Kohana::$log->add(Log::INFO, "Status: " . $response->status());
                Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
            }
        }
        Auth::instance()->logout();
    }

    protected function nightly(Model_Build &$build)
    {
        if (!empty($build->project->phing_target_nightly)) {
            chdir((empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path));

            Kohana::$log->add(Log::INFO, "Starting deploying nightly...");
            $updateLog = array();
            exec(
                    'phing -logger phing.listener.HtmlColorLogger -logfile nightlylog.html ' . $build->project->phing_target_nightly,
                    $updateLog, $updateResult
            );
            if (!empty($updateLog)) {
                Kohana::$log->add(Log::INFO, "Additional log: " . implode("\n", $updateLog));
            }
            Kohana::$log->add(Log::INFO, "Finished deploying nightly with result $updateResult");
            rename('nightlylog.html', $this->_outdir_owaka . 'nightlylog.html');

            if ($updateResult == 0) {
                Kohana::$log->add(Log::INFO, "Update successful");
            } else if ($updateResult == 1) {
                Kohana::$log->add(Log::INFO, "Update failed with errors");
            } else {
                Kohana::$log->add(Log::INFO, "Update unproperly configured");
            }
        }
    }

    protected function parseReports(Model_Build &$build)
    {
        Auth::instance()->force_login('owaka');
        foreach (File::findProcessors() as $processor) {
            $name     = str_replace("Controller_", "", $processor);
            Kohana::$log->add(Log::INFO, "Processing reports for $name...");
            $response = Request::factory($name . '/process/' . $build->id)
                    ->execute();
            if ($response->status() != 200) {
                Kohana::$log->add(Log::INFO, "Status: " . $response->status());
                Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
            }
        }
        Auth::instance()->logout();
    }

    protected function analyzeReports(Model_Build &$build)
    {
        // Do not update if status is already set (-> error)
        if ($build->status == 'building') {
            $build->status = 'ok';
            Auth::instance()->force_login('owaka');

            foreach (File::findAnalyzers() as $processor) {
                $name     = str_replace("Controller_", "", $processor);
                Kohana::$log->add(Log::INFO, "Analyzing reports for $name...");
                $response = Request::factory($name . '/analyze/' . $build->id)
                        ->execute();
                if ($response->status() != 200) {
                    Kohana::$log->add(Log::INFO, "Status: " . $response->status());
                    Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
                }

                if ($response->body() == 'error') {
                    $build->status = 'error';
                    break;
                } else if ($response->body() == 'unstable') {
                    $build->status = 'unstable';
                }
            }
            $build->finished = DB::expr('NOW()');
            $build->update();
            Auth::instance()->logout();
        } else {
            Kohana::$log->add(Log::INFO, "Skipping analyze of reports: status already set to " . $build->status);
        }
    }
}