<?php

class Controller_Data_Unittest extends Controller_Data_Base
{

    public function action_parse()
    {
        $build  = $this->request->param('id');
        $report = Helper_Owaka::getReportsPath($build, 'phpunit');

        if (file_exists($report) && file_get_contents($report) != "") {
            $global           = ORM::factory('phpunit_globaldata');
            $global->build_id = $build;
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
                            $error->build_id  = $build;
                            $error->testsuite = (string) $subtestsuite['name'];
                            $error->testcase  = (string) $testcase['name'];
                            $error->create();
                        }
                    }
                }
            }

            $global->create();
            $this->response->body(true);
        }

        $this->response->body(false);
    }
}
