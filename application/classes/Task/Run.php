<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Task for running the first build in the queue
 * 
 * @package   Task
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Task_Run extends Minion_Task
{

    private $_outdir       = NULL;
    private $_outdir_owaka = NULL;
    // @codingStandardsIgnoreStart

    /**
     * Executes the task
     * 
     * @param array $params Parameters
     * 
     * @SuppressWarnings("unused")
     */
    protected function _execute(array $params)
    {
        $build = ORM::factory('Build')
                ->where('status', '=', Owaka::BUILD_QUEUED)
                ->order_by('started', 'ASC')
                ->order_by('id', 'ASC')
                ->limit(1)
                ->find();

        if (!$build->loaded()) {
            echo 'No project to build';
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
        echo 'ok';
    }
    // @codingStandardsIgnoreEnd

    /**
     * Validates build, running all the tasks
     * 
     * @param Model_Build &$build Build
     */
    protected function validate(Model_Build &$build)
    {
        // Get last build duration to compute ETA
        $lastBuild    = $build->project->builds
                ->where('status', 'NOT IN', array(Owaka::BUILD_QUEUED, Owaka::BUILD_BUILDING))
                ->order_by('id', 'DESC')
                ->limit(1)
                ->find();
        $lastStart    = new DateTime($lastBuild->started);
        $lastFinished = new DateTime($lastBuild->finished);
        $lastDuration = $lastFinished->diff($lastStart, TRUE);

        $build->status  = Owaka::BUILD_BUILDING;
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

            $buildTargetLog = $command->execute(
                    'phing -logger phing.listener.HtmlColorLogger ' . $target . ' -Dowaka.build=' . $build->id
            );
            if (strpos($buildTargetLog, 'BUILD FINISHED')) {
                $buildTargetResult = 0;
            } else {
                $buildTargetResult = 1;
            }
            Kohana::$log->add(Log::INFO, "Finished $target with result $buildTargetResult");

            file_put_contents($this->_outdir_owaka . 'buildlog.html', "<h1>Target $target</h1>", FILE_APPEND);
            file_put_contents($this->_outdir_owaka . 'buildlog.html', $buildTargetLog, FILE_APPEND);
            file_put_contents(
                    $this->_outdir_owaka . 'buildlog.html',
                    "<h1>End of target $target with result $buildTargetResult</h1>", FILE_APPEND
            );

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
                $build->status = Owaka::BUILD_ERROR;   // Build unproperly configured
                break;
            }
        }

        $build->update();
        $build->project->lastrevision = $build->revision;
        $build->project->update();
    }

    /**
     * Copy reports from a build
     * 
     * @param Model_Build &$build Build
     */
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

    /**
     * Processors reports
     * 
     * @param Model_Build &$build Build
     */
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

    /**
     * Analyses reports
     * 
     * @param Model_Build &$build Build
     */
    protected function analyzeReports(Model_Build &$build)
    {
        // Do not update if status is already set (-> error)
        Auth::instance()->force_login('owaka');
        if ($build->status == Owaka::BUILD_BUILDING) {
            $build->status = Owaka::BUILD_OK;

            foreach (File::findAnalyzers() as $processor) {
                $name     = str_replace("Controller_", "", $processor);
                Kohana::$log->add(Log::INFO, "Analyzing reports for $name...");
                $response = Request::factory($name . '/analyze/' . $build->id)
                        ->execute();
                if ($response->status() != 200) {
                    Kohana::$log->add(Log::ERROR, "Status: " . $response->status());
                    Kohana::$log->add(Log::ERROR, "Content: " . $response->body());
                }

                if ($response->body() == Owaka::BUILD_ERROR) {
                    $build->status = Owaka::BUILD_ERROR;
                    break;
                } else if ($response->body() == Owaka::BUILD_UNSTABLE) {
                    $build->status = Owaka::BUILD_UNSTABLE;
                }
                Kohana::$log->add(Log::INFO, "$name : {$build->status}");
            }
        } else {
            Kohana::$log->add(Log::INFO, "Skipping analyze of reports: status already set to " . $build->status);
        }
        $build->finished = DB::expr('NOW()');
        $build->update();
        Auth::instance()->logout();
    }
}
