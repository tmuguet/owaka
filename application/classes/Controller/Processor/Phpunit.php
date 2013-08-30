<?php

/**
 * phpunit
 * 
 * @package Processors
 */
class Controller_Processor_Phpunit extends Controller_Processor
{

    /**
     * Gets the input reports
     * 
     * @return array
     */
    static public function inputReports()
    {
        return array(
            'xml'    => array(
                'title'       => 'XML report',
                'description' => 'PHPUnit XML report with xml format. This is the report used for processing data.',
                'type'        => 'file',
                'keep-as'     => 'report.xml',
                'analysis'    => true
            ),
            'report' => array(
                'title'       => 'HTML report',
                'description' => 'PHPUnit HTML report directory',
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
            'threshold_errors_error'      => array(
                'title'        => 'Number of errors to trigger build error',
                'defaultvalue' => 1
            ),
            'threshold_errors_unstable'   => array(
                'title'        => 'Number of errors to trigger unstable build',
                'defaultvalue' => -1
            ),
            'threshold_failures_error'    => array(
                'title'        => 'Number of failures to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_failures_unstable' => array(
                'title'        => 'Number of failures to trigger unstable build',
                'defaultvalue' => 1
            )
        );
    }

    /**
     * Processes a PHPUnit XML report
     * 
     * @param int $buildId Build ID
     * 
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = $this->getReportCompletePath($buildId, 'xml');

        if (!empty($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('Phpunit_Globaldata');
            $global->build_id = $buildId;
            $global->tests    = 0;
            $global->failures = 0;
            $global->errors   = 0;
            $global->time     = 0;

            $xml = simplexml_load_file($report);
            foreach ($xml->children() as $testsuite) {
                $global->tests += (int) $testsuite['tests'];
                $global->failures += (int) $testsuite['failures'];
                $global->errors += (int) $testsuite['errors'];
                $global->time += (double) $testsuite['time'];

                foreach ($testsuite->children() as $subtestsuite) {
                    foreach ($subtestsuite->children() as $testcase) {
                        if ($testcase->count() > 0) {
                            $error            = ORM::factory('Phpunit_Error');
                            $error->build_id  = $buildId;
                            $error->testsuite = (string) $subtestsuite['name'];
                            $error->testcase  = (string) $testcase['name'];
                            $error->create();
                        }
                    }
                }
            }

            $global->create();
            return true;
        }

        return false;
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
        $data = $build->phpunit_globaldata;
        
        if (($parameters['threshold_errors_error'] > 0 && $data->errors >= $parameters['threshold_errors_error']) 
                || ($parameters['threshold_failures_error'] > 0 && $data->failures >= $parameters['threshold_failures_error'])) {
            return Owaka::BUILD_ERROR;
        } else if (($parameters['threshold_errors_unstable'] > 0 && $data->errors >= $parameters['threshold_errors_unstable'])
                || ($parameters['threshold_failures_unstable'] > 0 && $data->failures >= $parameters['threshold_failures_unstable'])) {
            return Owaka::BUILD_UNSTABLE;
        } else {
            return Owaka::BUILD_OK;
        }
    }
}
