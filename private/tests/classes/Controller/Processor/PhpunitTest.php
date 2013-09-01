<?php

class Controller_Processor_PhpunitTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build2'];
        $this->target->request->setParam('id', $this->buildId);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Controller_Processor_Phpunit::process
     * @covers Controller_Processor_Phpunit::findRegressions
     */
    public function testProcess()
    {
        $this->CopyReport(
                'xml', dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'phpunit-report.xml'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(
            array(
                'tests'                => 86,
                'failures'             => 1,
                'errors'               => 2,
                'time'                 => 15.54,
                'tests_delta'          => 81,
                'failures_regressions' => 1,
                'failures_fixed'       => 1,
                'errors_regressions'   => 1,
                'errors_fixed'         => 1
            )
        );
        $globaldata         = DB::select(
                                'tests', 'failures', 'errors', 'time', 'tests_delta', 'failures_regressions',
                                'failures_fixed', 'errors_regressions', 'errors_fixed'
                        )
                        ->from('phpunit_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data7'])
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');

        $dataExpected = array(
            array("testsuite"  => "Controller_Processor_PhpunitTest", "testcase"   => "testProcess", "regression" => 1),
            array("testsuite"  => "Controller_Processor_PhpunitTest", "testcase"   => "testProcessEmpty", "regression" => 0),
            array("testsuite"  => "Controller_ReportTest", "testcase"   => "testActionIndex", "regression" => 1)
        );
        $data         = DB::select('testsuite', 'testcase', 'regression')
                        ->from('phpunit_errors')
                        ->order_by('id', 'ASC')
                        ->where('id', '!=', $this->genNumbers['data6'])
                        ->where('id', '!=', $this->genNumbers['data10'])
                        ->where('id', '!=', $this->genNumbers['data11'])
                        ->execute()->as_array();
        $this->assertEquals($dataExpected, $data);
    }

    /**
     * @covers Controller_Processor_Phpunit::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $globaldata = DB::select('tests', 'failures', 'errors', 'time')
                        ->from('phpunit_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data7'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');

        $data = DB::select('testsuite', 'testcase')
                        ->from('phpunit_errors')
                        ->where('id', '!=', $this->genNumbers['data6'])
                        ->where('id', '!=', $this->genNumbers['data10'])
                        ->where('id', '!=', $this->genNumbers['data11'])
                        ->execute()->as_array();
        $this->assertEmpty($data);
    }

    /**
     * @covers Controller_Processor_Phpunit::analyze
     */
    public function testAnalyze()
    {
        $thresholds                           = array(
            'threshold_errors_error'      => 1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_error'    => -1,
            'threshold_failures_unstable' => 1
        );
        $model1                               = ORM::factory('Build');
        $model1->phpunit_globaldata->failures = 0;
        $model1->phpunit_globaldata->errors   = 0;
        $this->assertEquals(Owaka::BUILD_OK, $this->target->analyze($model1, $thresholds));

        $model2                               = ORM::factory('Build');
        $model2->phpunit_globaldata->failures = 1;
        $model2->phpunit_globaldata->errors   = 0;
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $this->target->analyze($model2, $thresholds));

        $model3                               = ORM::factory('Build');
        $model3->phpunit_globaldata->failures = 0;
        $model3->phpunit_globaldata->errors   = 1;
        $this->assertEquals(Owaka::BUILD_ERROR, $this->target->analyze($model3, $thresholds));

        $model4                               = ORM::factory('Build');
        $model4->phpunit_globaldata->failures = 1;
        $model4->phpunit_globaldata->errors   = 1;
        $this->assertEquals(Owaka::BUILD_ERROR, $this->target->analyze($model4, $thresholds));
    }
}
