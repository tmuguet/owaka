<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * PHPMD
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Processor_Phpmd extends Processor
{

    public static $inputReports = array(
        'html' => array(
            'title'       => 'HTML report',
            'description' => 'PHPMD HTML report. This is the report used for processing data.',
            'type'        => 'file',
            'keep-as'     => 'report.html',
        )
    );
    public static $parameters   = array(
        'threshold_errors_error'    => array(
            'title'        => 'Errors to trigger error',
            'description'  => 'Number of errors to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_errors_unstable' => array(
            'title'        => 'Errors to trigger unstable',
            'description'  => 'Number of errors to trigger unstable build',
            'defaultvalue' => 1
        ),
        'threshold_errors_delta_error'    => array(
            'title'        => 'Regression errors to trigger error',
            'description'  => 'Number of regression errors to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_errors_delta_unstable' => array(
            'title'        => 'Regression errors to trigger unstable',
            'description'  => 'Number of regression errors to trigger unstable build',
            'defaultvalue' => 1
        )
    );

    /**
     * Processes a PHPMD HTML report
     * 
     * @param Model_Build &$build Build
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process(Model_Build &$build)
    {
        $report = $this->getReportCompleteRealPath($build, 'html');

        if (!empty($report) && file_get_contents($report) != '') {
            $content          = file_get_contents($report);
            $global           = ORM::factory('Phpmd_Globaldata');
            $global->build_id = $build->id;
            $global->errors   = substr_count($content, '</tr>'); // - 1;
            // bug in phpmd: header row not terminated with </tr>

            $this->findDeltas($build, $global);
            $global->create();
            return true;
        }

        return false;
    }

    /**
     * Computes deltas with previous build
     * 
     * @param Model_Build            &$build Build
     * @param Model_Phpmd_Globaldata &$data  Current data
     */
    protected function findDeltas(Model_Build &$build, Model_Phpmd_Globaldata &$data)
    {
        $prevBuild = $build->previousBuild()->find();
        $prevData  = $prevBuild->phpmd_globaldata;
        if ($prevData->loaded()) {
            $data->errors_delta = $data->errors - $prevData->errors;
        }
    }

    /**
     * Analyses a build
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Processor parameters
     * 
     * @return string Status
     */
    public function analyze(Model_Build &$build, array $parameters)
    {
        return $build->phpmd_globaldata->buildStatus($parameters);
    }
}