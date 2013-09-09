<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * phpunit
 * 
 * @package   Processors
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
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
                'title'        => 'Errors to trigger error',
                'description'  => 'Number of errors to trigger build error',
                'defaultvalue' => 1
            ),
            'threshold_errors_unstable'   => array(
                'title'        => 'Errors to trigger unstable',
                'description'  => 'Number of errors to trigger unstable build',
                'defaultvalue' => -1
            ),
            'threshold_failures_error'    => array(
                'title'        => 'Failures to trigger error',
                'description'  => 'Number of failures to trigger build error',
                'defaultvalue' => -1
            ),
            'threshold_failures_unstable' => array(
                'title'        => 'Failures to trigger unstable',
                'description'  => 'Number of failures to trigger unstable build',
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
                            $errorNodes = $testcase->children();

                            $error            = ORM::factory('Phpunit_Error');
                            $error->build_id  = $buildId;
                            $error->testsuite = (string) $subtestsuite['name'];
                            $error->testcase  = (string) $testcase['name'];
                            $error->severity  = $errorNodes[0]->getName();
                            $error->create();
                        }
                    }
                }
            }

            $this->findRegressions($global);
            $global->create();
            return true;
        }

        return false;
    }

    /**
     * Finds regressions and fixes with previous build
     * 
     * @param Model_Phpunit_Globaldata &$data Current data
     */
    protected function findRegressions(Model_Phpunit_Globaldata &$data)
    {
        $build     = $data->build;
        $prevBuild = $build->previousBuild()->find();
        if ($prevBuild->loaded()) {
            $data->failures_regressions = 0;
            $data->errors_regressions   = 0;
            $data->failures_fixed       = 0;
            $data->errors_fixed         = 0;
            $data->tests_delta          = $data->tests - $prevBuild->phpunit_globaldata->tests;
            $data->time_delta           = $data->time - $prevBuild->phpunit_globaldata->time;

            foreach ($build->phpunit_errors->find_all() as $current) {
                if (!$current->hasSimilar($prevBuild)) {
                    if ($current->severity == 'failure') {
                        $data->failures_regressions++;
                    } else {
                        $data->errors_regressions++;
                    }
                    $current->regression = 1;
                    $current->update();
                }
            }

            foreach ($prevBuild->phpunit_errors->find_all() as $old) {
                if (!$old->hasSimilar($build)) {
                    if ($old->severity == 'failure') {
                        $data->failures_fixed++;
                    } else {
                        $data->errors_fixed++;
                    }
                    $old->fixed = 1;
                    $old->update();
                }
            }
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
        return $build->phpunit_globaldata->buildStatus($parameters);
    }
}
