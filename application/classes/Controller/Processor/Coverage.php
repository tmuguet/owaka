<?php

/**
 * Coverage
 * 
 * @package Processors
 */
class Controller_Processor_Coverage extends Controller_Processor
{

    /**
     * Gets the input reports
     * 
     * @return array
     */
    static public function inputReports()
    {
        return array(
            'raw' => array(
                'title'       => 'XML report',
                'description' => 'Coverage XML report. This is the report used for processing data.',
                'type'        => 'file',
                'keep-as'     => 'coverage.xml'
            ),
            'dir' => array(
                'title'       => 'HTML report',
                'description' => 'Coverage HTML report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
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
            'threshold_methodcoverage_error'       => array(
                'title'        => 'Method coverage to trigger error',
                'description'  => 'Threshold of method coverage to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_methodcoverage_unstable'    => array(
                'title'        => 'Method coverage to trigger unstable',
                'description'  => 'Threshold of method coverage to trigger unstable build',
                'defaultvalue' => 100
            ),
            'threshold_statementcoverage_error'    => array(
                'title'        => 'Statement coverage to trigger error',
                'description'  => 'Threshold of statement coverage to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_statementcoverage_unstable' => array(
                'title'        => 'Statement coverage to trigger unstable',
                'description'  => 'Threshold of statement coverage to trigger unstable build',
                'defaultvalue' => -1
            ),
            'threshold_totalcoverage_error'        => array(
                'title'        => 'Total coverage to trigger error',
                'description'  => 'Threshold of total coverage to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_totalcoverage_unstable'     => array(
                'title'        => 'Total coverage to trigger unstable',
                'description'  => 'Threshold of total coverage to trigger unstable build',
                'defaultvalue' => -1
            ),
        );
    }

    /**
     * Processes a coverage XML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available or if empty report
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'raw');

        if (!empty($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('Coverage_Globaldata');
            $global->build_id = $buildId;

            $xml                    = simplexml_load_file($report);
            $global->methodcount    = (int) $xml['methodcount'];
            $global->methodscovered = (int) $xml['methodscovered'];
            $global->methodcoverage = ($global->methodcount > 0 ?
                            ($global->methodscovered * 100 / $global->methodcount) : 100);

            $global->statementcount    = (int) $xml['statementcount'];
            $global->statementscovered = (int) $xml['statementscovered'];
            $global->statementcoverage = ($global->statementcount > 0 ?
                            ($global->statementscovered * 100 / $global->statementcount) : 100);

            $global->totalcount    = (int) $xml['totalcount'];
            $global->totalcovered  = (int) $xml['totalcovered'];
            $global->totalcoverage = ($global->totalcount > 0 ?
                            ($global->totalcovered * 100 / $global->totalcount) : 100);

            if ($global->methodcount > 0 || $global->statementcount > 0 || $global->totalcount > 0) {
                $this->findDeltas($global);
                $global->create();
            }
            return true;
        }

        return false;
    }

    /**
     * Computes deltas with previous build
     * 
     * @param Model_Coverage_Globaldata &$data Current data
     */
    protected function findDeltas(Model_Coverage_Globaldata &$data)
    {
        $build     = $data->build;
        $prevBuild = $build->previousBuild()->find();
        $prevData  = $prevBuild->coverage_globaldata;
        if ($prevData->loaded()) {
            $data->methodcoverage_delta    = $data->methodcoverage - $prevData->methodcoverage;
            $data->statementcoverage_delta = $data->statementcoverage - $prevData->statementcoverage;
            $data->totalcoverage_delta     = $data->totalcoverage - $prevData->totalcoverage;
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
        return $build->coverage_globaldata->buildStatus($parameters);
    }
}
