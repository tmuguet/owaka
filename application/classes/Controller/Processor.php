<?php

/**
 * Base class for all processors
 * 
 * @package Processors
 */
abstract class Controller_Processor extends Controller
{

    protected $requiredRole = Owaka::AUTH_ROLE_INTERNAL;

    /**
     * Gets the input reports
     * 
     * @throws Exception
     * @todo Document this
     */
    static public function getInputReports()
    {
        throw new Exception('Not implemented');
    }

    /**
     * Processes a report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     * @throws Exception Broken report (unexpected format)
     */
    abstract public function process($buildId);

    /**
     * Copies all available reports for the processor in owaka's data directory
     */
    public final function action_copy()
    {
        $buildId              = $this->request->param('id');    // TODO: validate
        $destinationDirectory = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $buildId
                . DIRECTORY_SEPARATOR . $this->_getName() . DIRECTORY_SEPARATOR;


        foreach (static::getInputReports() as $type => $info) {
            $source      = $this->_getInputReportCompletePath($buildId, $type);
            $destination = $destinationDirectory . $info['keep-as'];    // TODO: this can get messy if mis-used !

            Kohana::$log->add(Log::DEBUG, "Trying to copy $source to $destination");

            if (!empty($source) && !empty($destination)) {
                if ($info['type'] == "dir" && !is_dir($source)) {
                    Kohana::$log->add(Log::INFO, "Source $source is not a directory");
                    continue;
                } else if ($info['type'] == "file" && !is_file($source)) {
                    Kohana::$log->add(Log::INFO, "Source $source is not a file");
                    continue;
                }

                Kohana::$log->add(Log::DEBUG, "Copying $source to $destination");
                if (!file_exists($destinationDirectory)) {
                    // Create the directory only if at least one report is available
                    mkdir($destinationDirectory, 0700, true);
                }

                File::rcopy($source, $destination);
            }
        }
    }

    /**
     * Gets the name of the processor being called
     * 
     * @return string
     */
    /* private */ final function _getName()
    {
        return strtolower(str_replace("Controller_Processor_", "", get_called_class()));
    }

    /**
     * Gets the canonical report name for a report-type, as stored in the DB
     * 
     * @param string $type Type of report to get
     * 
     * @return string
     * @see getInputReports() for report types
     */
    /* private */ final function _getReportName($type)
    {
        return $this->_getName() . '_' . $type;
    }

    /**
     * Gets the path of an input report, or NULL if no report available
     * 
     * @param int    $buildId Build ID
     * @param string $type    Type of report to get
     * 
     * @return string|null
     * @see getInputReports() for report types
     */
    /* private */ final function _getInputReportCompletePath($buildId, $type)
    {
        $build = ORM::factory('Build', $buildId);
        $name  = ORM::factory('Project_Report')->search($build->project_id, $this->_getReportName($type));
        if (empty($name)) {
            return NULL;
        }

        $reportPath = str_replace(
                array('%rev%', '%build%'), array($build->revision, $build->id), $build->project->reports_path
        );
        if (substr($reportPath, 0, -1) != DIRECTORY_SEPARATOR) {
            $reportPath .= DIRECTORY_SEPARATOR;
        }
        $realPath = realpath($reportPath . $name);
        if (empty($realPath)) {
            return NULL;
        } else {
            return $realPath;
        }
    }

    /**
     * Gets the path of a report in owaka's data directory, or NULL if not available
     * 
     * @param int    $buildId Build ID
     * @param string $type    Type of report to get
     * 
     * @return string|null
     * @see getInputReports() for report types
     */
    /* protected */ final function getReportCompletePath($buildId, $type)
    {
        $destination = APPPATH . 'reports' . DIRECTORY_SEPARATOR . $buildId . DIRECTORY_SEPARATOR
                . $this->_getName() . DIRECTORY_SEPARATOR;
        $reports     = static::getInputReports();
        if (!isset($reports[$type]) || !isset($reports[$type]['keep-as'])) {
            return NULL;
        }
        $path = realpath($destination . $reports[$type]['keep-as']);
        if (!empty($path)) {
            return $path;
        } else {
            return NULL;
        }
    }

    /**
     * Processes the reports
     * 
     * @url http://example.com/processor_&lt;processor&gt;/process/&lt;build_id&gt;
     */
    public final function action_process()
    {
        $buildId = $this->request->param('id');
        $result  = $this->process($buildId);
        // TODO: proper error management
        if ($result) {
            $this->response->body("true");
        } else {
            $this->response->body("false");
        }
    }

    /**
     * Analyzes the reports to determine the build status
     * 
     * @url http://example.com/processor_&lt;processor&gt;/analyze/&lt;build_id&gt;
     */
    public final function action_analyze()
    {
        $buildId = $this->request->param('id');
        $result  = NULL;
        if (method_exists($this, 'analyze')) {
            $build  = ORM::factory('Build', $buildId);
            $result = $this->analyze($build);
        }

        $this->response->body($result);
    }
}