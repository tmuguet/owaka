<?php

/**
 * Unittest (phpunit)
 */
class Controller_Processors_Unittest extends Controller_Processors_Base
{

    /**
     * Processes a PHPUnit XML report
     * @param int $buildId Build ID
     * @return bool true if report successfully treated; false if no report available
     */
    public function process($buildId)
    {
        $report = Owaka::getReportsPath($buildId, 'phpunit');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('phpunit_globaldata');
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
                            $error            = ORM::factory('phpunit_error');
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
}
