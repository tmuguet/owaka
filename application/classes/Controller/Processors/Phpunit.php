<?php

/**
 * phpunit
 * 
 * @package Processors
 */
class Controller_Processors_Phpunit extends Controller_Processors_Base
{

    static public function getInputReports()
    {
        return array(
            'xml'    => array(
                'title'       => 'PHPUnit XML report',
                'description' => 'PHPUnit XML report with xml format',
                'type'        => 'file',
                'keep-as'     => 'report.xml'
            ),
            'report' => array(
                'title'       => 'PHPUnit report',
                'description' => 'PHPUnit HTML report directory',
                'type'        => 'dir',
                'keep-as'     => '.'
            )
        );
    }

    /**
     * Processes a PHPUnit XML report
     * @param int $buildId Build ID
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

    public function analyze(Model_Build &$build)
    {
        if ($build->phpunit_globaldata->failures == 0 && $build->phpunit_globaldata->errors == 0) {
            return 'ok';
        } else if ($build->phpunit_globaldata->errors == 0) {
            return 'unstable';
        } else {
            return 'error';
        }
    }
}
