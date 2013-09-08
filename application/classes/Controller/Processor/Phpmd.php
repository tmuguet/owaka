<?php

/**
 * PHPMD
 * 
 * @package Processors
 */
class Controller_Processor_Phpmd extends Controller_Processor
{

    /**
     * Gets the input reports
     * 
     * @return array
     */
    static public function inputReports()
    {
        return array(
            'html' => array(
                'title'       => 'HTML report',
                'description' => 'PHPMD HTML report. This is the report used for processing data.',
                'type'        => 'file',
                'keep-as'     => 'report.html',
            )
        );
    }

    /**
     * Gets the processor parameters
     * 
     * @return array
     */
    static public function parameters()
    {
        return array(
            'threshold_errors_error'    => array(
                'title'        => 'Errors to trigger error',
                'description'  => 'Number of errors to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_errors_unstable' => array(
                'title'        => 'Errors to trigger unstable',
                'description'  => 'Number of errors to trigger unstable build',
                'defaultvalue' => 1
            )
        );
    }

    /**
     * Processes a PHPMD HTML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'html');

        if (!empty($report) && file_get_contents($report) != "") {
            $content          = file_get_contents($report);
            $global           = ORM::factory('Phpmd_Globaldata');
            $global->build_id = $buildId;
            $global->errors   = substr_count($content, '</tr>'); // - 1;
            // bug in phpmd: header row not terminated with </tr>

            $this->findDeltas($global);
            $global->create();
            return true;
        }

        return false;
    }

    /**
     * Computes deltas with previous build
     * 
     * @param Model_Phpmd_Globaldata &$data Current data
     */
    protected function findDeltas(Model_Phpmd_Globaldata &$data)
    {
        $build     = $data->build;
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