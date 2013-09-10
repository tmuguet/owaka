<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Base class for all processors
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
    static public function inputReports()
    {
        throw new Exception('Not implemented');
    }

    /**
     * Gets the processor parameters
     * 
     * @return array
     */
    static public function parameters()
    {
        return array();
    }

    /**
     * Gets the processor parameters
     * 
     * @param int $projectId Project ID
     * 
     * @return array
     */
    static public function projectParameters($projectId)
    {
        $params     = ORM::factory('Project_Report_Parameter')
                ->where('project_id', '=', $projectId)
                ->where('processor', '=', static::_getName())
                ->find_all();
        $parameters = array();
        foreach (static::parameters() as $key => $info) {
            $parameters[$key] = $info['defaultvalue'];
        }
        foreach ($params as $param) {
            if ($param->value != -1) {
                $parameters[$param->type] = $param->value;
            }
        }
        return $parameters;
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
        $build                = ORM::factory('Build', $buildId);
        $destinationDirectory = APPPATH . 'reports' . DIR_SEP . $buildId
                . DIR_SEP . $this->_getName() . DIR_SEP;


        $command = new Command($build->project);

        foreach (static::inputReports() as $type => $info) {
            $source      = $this->_getInputReportCompletePath($buildId, $type);
            $destination = $destinationDirectory . $info['keep-as'];    // TODO: this can get messy if mis-used !

            Kohana::$log->add(Log::DEBUG, "Trying to copy $source to $destination");

            if (!empty($source) && !empty($destination)) {
                if ($info['type'] == "dir" && !$command->is_dir($source)) {
                    Kohana::$log->add(Log::INFO, "Source $source is not a directory");
                    continue;
                } else if ($info['type'] == "file" && !$command->is_file($source)) {
                    Kohana::$log->add(Log::INFO, "Source $source is not a file");
                    continue;
                }

                Kohana::$log->add(Log::DEBUG, "Copying $source to $destination");
                if (!file_exists($destinationDirectory)) {
                    // Create the directory only if at least one report is available
                    mkdir($destinationDirectory, 0700, true);
                }

                $command->rcopy($source, $destination);
            }
        }
    }

    /**
     * Gets the name of the processor being called
     * 
     * @return string
     */
    static /* private */ final function _getName()
    {
        return strtolower(str_replace("Controller_Processor_", "", get_called_class()));
    }

    /**
     * Gets the canonical report name for a report-type, as stored in the DB
     * 
     * @param string $type Type of report to get
     * 
     * @return string
     * @see inputReports() for report types
     */
    /* private */ final function _getReportName($type)
    {
        return static::_getName() . '_' . $type;
    }

    /**
     * Gets the path of an input report, or NULL if no report available
     * 
     * @param int    $buildId Build ID
     * @param string $type    Type of report to get
     * 
     * @return string|null
     * @see inputReports() for report types
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
        if (substr($reportPath, 0, -1) != DIR_SEP) {
            $reportPath .= DIR_SEP;
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
     * @see inputReports() for report types
     */
    /* protected */ final function getReportCompletePath($buildId, $type)
    {
        $destination = APPPATH . 'reports' . DIR_SEP . $buildId . DIR_SEP
                . $this->_getName() . DIR_SEP;
        $reports     = static::inputReports();
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
        $result = NULL;
        if (method_exists($this, 'analyze')) {
            $buildId = $this->request->param('id');
            $build   = ORM::factory('Build', $buildId);
            $result  = $this->analyze($build, static::projectParameters($build->project_id));
        }

        $this->response->body($result);
    }
}