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
abstract class Processor
{

    /**
     * Input reports
     * @var array
     * @todo Document this
     */
    public static $inputReports = array();

    /**
     * Processor parameters
     * @var array
     * @todo Document this
     */
    public static $parameters = array();

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
        foreach (static::$parameters as $key => $info) {
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
     * @param Model_Build &$build Build
     * 
     * @return bool true if report successfully treated; false if no report available
     * @throws Exception Broken report (unexpected format)
     */
    abstract public function process(Model_Build &$build);

    /**
     * Gets the name of the processor being called
     * 
     * @return string
     */
    static /* private */ final function _getName()
    {
        return strtolower(str_replace('Processor_', '', get_called_class()));
    }

    /**
     * Gets the path of an input report, or NULL if no report available
     * 
     * @param Model_Build &$build Build
     * @param string      $type   Type of report to get
     * 
     * @return string|null
     * @see inputReports for report types
     */
    public final function getInputReportCompleteRealPath(Model_Build &$build, $type)
    {
        $name = ORM::factory('Project_Report')->search($build->project_id, static::_getName() . '_' . $type);
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
     * Gets the root path of all reports in owaka's data directory.
     * This does not guarantee the path exists.
     * 
     * @param Model_Build &$build Build
     * 
     * @return string|null
     */
    public final function getReportRootPath(Model_build &$build)
    {
        return APPPATH . 'reports' . DIR_SEP . $build->id . DIR_SEP . static::_getName() . DIR_SEP;
    }

    /**
     * Gets the path of a report in owaka's data directory, or NULL if not available.
     * This does not guarantee the path exists. Use getReportCompleteRealPath when possible.
     * 
     * @param Model_Build &$build Build
     * @param string      $type   Type of report to get
     * 
     * @return string|null
     * @see inputReports for report types
     * @see getReportCompleteRealPath
     */
    public final function getReportCompletePath(Model_build &$build, $type)
    {
        $destination = $this->getReportRootPath($build);
        $reports     = static::$inputReports;
        if (!isset($reports[$type]) || !isset($reports[$type]['keep-as'])) {
            return NULL;
        }
        return $destination . $reports[$type]['keep-as'];
    }

    /**
     * Gets the path of a report in owaka's data directory, or NULL if not existing or not available
     * 
     * @param Model_Build &$build Build
     * @param string      $type   Type of report to get
     * 
     * @return string|null
     * @see inputReports for report types
     */
    public final function getReportCompleteRealPath(Model_Build &$build, $type)
    {
        $path = $this->getReportCompletePath($build, $type);
        $realpath = realpath($path);
        if (!empty($path) && !empty($realpath)) {
            return $realpath;
        } else {
            return NULL;
        }
    }
}