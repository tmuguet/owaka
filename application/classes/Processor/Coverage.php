<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Coverage
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Processor_Coverage extends Processor
{

    public static $inputReports = array(
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
    public static $parameters   = array(
        'threshold_methodcoverage_error'             => array(
            'title'        => 'Method coverage to trigger error',
            'description'  => 'Threshold of method coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_methodcoverage_unstable'          => array(
            'title'        => 'Method coverage to trigger unstable',
            'description'  => 'Threshold of method coverage to trigger unstable build',
            'defaultvalue' => 100
        ),
        'threshold_statementcoverage_error'          => array(
            'title'        => 'Statement coverage to trigger error',
            'description'  => 'Threshold of statement coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_statementcoverage_unstable'       => array(
            'title'        => 'Statement coverage to trigger unstable',
            'description'  => 'Threshold of statement coverage to trigger unstable build',
            'defaultvalue' => 100
        ),
        'threshold_totalcoverage_error'              => array(
            'title'        => 'Total coverage to trigger error',
            'description'  => 'Threshold of total coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_totalcoverage_unstable'           => array(
            'title'        => 'Total coverage to trigger unstable',
            'description'  => 'Threshold of total coverage to trigger unstable build',
            'defaultvalue' => 100
        ),
        'threshold_methodcoverage_delta_error'       => array(
            'title'        => 'Delta in method coverage to trigger error',
            'description'  => 'Threshold of delta in method coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_methodcoverage_delta_unstable'    => array(
            'title'        => 'Delta in method coverage to trigger unstable',
            'description'  => 'Threshold of delta in method coverage to trigger unstable build',
            'defaultvalue' => 0.01
        ),
        'threshold_statementcoverage_delta_error'    => array(
            'title'        => 'Delta in statement coverage to trigger error',
            'description'  => 'Threshold of delta in statement coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_statementcoverage_delta_unstable' => array(
            'title'        => 'Delta in statement coverage to trigger unstable',
            'description'  => 'Threshold of delta in statement coverage to trigger unstable build',
            'defaultvalue' => 0.01
        ),
        'threshold_totalcoverage_delta_error'        => array(
            'title'        => 'Delta in total coverage to trigger error',
            'description'  => 'Threshold of delta in total coverage to trigger build error',
            'defaultvalue' => -1
        ),
        'threshold_totalcoverage_delta_unstable'     => array(
            'title'        => 'Delta in total coverage to trigger unstable',
            'description'  => 'Threshold of delta in total coverage to trigger unstable build',
            'defaultvalue' => 0.01
        ),
    );

    /**
     * Processes a coverage XML report
     * 
     * @param Model_Build &$build Build
     * 
     * @return bool true if report successfully treated; false if no report available or if empty report
     */
    public function process(Model_Build &$build)
    {
        $report = $this->getReportCompleteRealPath($build, 'raw');

        if (!empty($report) && file_get_contents($report) != '') {
            $global           = ORM::factory('Coverage_Globaldata');
            $global->build_id = $build->id;

            $xml                    = simplexml_load_file($report);
            $global->methodcount    = (int) $xml['methodcount'];
            $global->methodscovered = (int) $xml['methodscovered'];
            $global->methodcoverage = Num::percent($global->methodscovered, $global->methodcount);

            $global->statementcount    = (int) $xml['statementcount'];
            $global->statementscovered = (int) $xml['statementscovered'];
            $global->statementcoverage = Num::percent($global->statementscovered, $global->statementcount);

            $global->totalcount    = (int) $xml['totalcount'];
            $global->totalcovered  = (int) $xml['totalcovered'];
            $global->totalcoverage = Num::percent($global->totalcovered, $global->totalcount);

            if ($global->methodcount > 0 || $global->statementcount > 0 || $global->totalcount > 0) {
                $this->findDeltas($build, $global);
                $global->create();
            }
            return true;
        }

        return false;
    }

    /**
     * Computes deltas with previous build
     * 
     * @param Model_Build               &$build Build
     * @param Model_Coverage_Globaldata &$data  Current data
     */
    protected function findDeltas(Model_Build &$build, Model_Coverage_Globaldata &$data)
    {
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
