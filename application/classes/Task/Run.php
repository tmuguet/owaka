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

        $build->status  = 'building';
        $build->started = DB::expr('NOW()');
        $build->eta     = DB::expr('ADDTIME(NOW(), \'' . $lastDuration->format('%H:%I:%S') . '\')');
        $build->update();

        $targets = array();
        $_tok    = strtok(trim($build->project->phing_target_validate), " ,;");

        while ($_tok !== false) {
            $targets[] = $_tok;
            $_tok      = strtok(" ,;");
        }
        
        $command = new Command($build->project);

        foreach ($targets as $target) {
            $path = (empty($build->project->phing_path) ? $build->project->path : $build->project->phing_path);
            $command->chdir($path);

            Kohana::$log->add(Log::INFO, "Starting $target...");

            $buildTargetLog = $command->execute('phing -logger phing.listener.HtmlColorLogger ' . $target . ' -Dowaka.build=' . $build->id);
            if (strpos($buildTargetLog, 'BUILD FINISHED')) {
                $buildTargetResult = 0;
            } else {
                $buildTargetResult = 1;
            }
            Kohana::$log->add(Log::INFO, "Finished $target with result $buildTargetResult");

            file_put_contents($this->_outdir_owaka . 'buildlog.html', "<h1>Target $target</h1>", FILE_APPEND);
            file_put_contents($this->_outdir_owaka . 'buildlog.html', $buildTargetLog, FILE_APPEND);
            file_put_contents($this->_outdir_owaka . 'buildlog.html', "<h1>End of target $target with result $buildTargetResult</h1>", FILE_APPEND);
            
            if ($buildTargetResult == 0) {
                Kohana::$log->add(Log::INFO, "Target $target successful");
                //$build->status = 'ok';    // Do not update yet
            } else if ($buildTargetResult == 1) {
                Kohana::$log->add(Log::ERROR, "Target $target failed with errors");
            } else {
                Kohana::$log->add(Log::CRITICAL, "Target $target unproperly configured");
            }
            
            $command->chtobasedir();

            $this->copyReports($build);
            
            if ($buildTargetResult != 0) {
                Kohana::$log->add(Log::INFO, "Stopping build");
                $build->status = 'error';   // Build unproperly configured
                break;
            }
        }

        $build->update();
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
                Kohana::$log->add(Log::ERROR, "Status: " . $response->status());
                Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
            }
        }
        Auth::instance()->logout();
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
                Kohana::$log->add(Log::ERROR, "Status: " . $response->status());
                Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
            }
        }
        Auth::instance()->logout();
    }

    protected function analyzeReports(Model_Build &$build)
    {
        // Do not update if status is already set (-> error)
        Auth::instance()->force_login('owaka');
        if ($build->status == 'building') {
            $build->status = 'ok';

            foreach (File::findAnalyzers() as $processor) {
                $name     = str_replace("Controller_", "", $processor);
                Kohana::$log->add(Log::INFO, "Analyzing reports for $name...");
                $response = Request::factory($name . '/analyze/' . $build->id)
                        ->execute();
                if ($response->status() != 200) {
                    Kohana::$log->add(Log::ERROR, "Status: " . $response->status());
                    Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
                }

                if ($response->body() == 'error') {
                    $build->status = 'error';
                    break;
                } else if ($response->body() == 'unstable') {
                    $build->status = 'unstable';
                }
            }
        } else {
            Kohana::$log->add(Log::INFO, "Skipping analyze of reports: status already set to " . $build->status);
        }
        $build->finished = DB::expr('NOW()');
        $build->update();
        Auth::instance()->logout();
    }
}
