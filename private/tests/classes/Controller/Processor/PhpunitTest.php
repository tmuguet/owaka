<?php

class Controller_Processor_PhpunitTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build1'];
        $this->target->request->setParam('id', $this->buildId);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Controller_Processor_Phpunit::process
     */
    public function testProcess()
    {
        $this->CopyReport(
                'xml', dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'phpunit-report.xml'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(array('tests'    => 86, 'failures' => 1, 'errors'   => 2, 'time'     => 15.54));
        $globaldata         = DB::select('tests', 'failures', 'errors', 'time')
                        ->from('phpunit_globaldatas')
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');

        $dataExpected = array(
            array("testsuite" => "Controller_Processor_PhpunitTest", "testcase"  => "testProcess"),
            array("testsuite" => "Controller_Processor_PhpunitTest", "testcase"  => "testProcessEmpty"),
            array("testsuite" => "Controller_ReportTest", "testcase"  => "testActionIndex")
        );
        $data         = DB::select('testsuite', 'testcase')
                        ->from('phpunit_errors')
                        ->order_by('id', 'ASC')
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
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');

        $data = DB::select('testsuite', 'testcase')
                        ->from('phpunit_errors')
                        ->execute()->as_array();
        $this->assertEmpty($data);
    }

    /**
     * @covers Controller_Processor_Phpunit::analyze
     */
    public function testAnalyze()
    {
        $thresholds                           = array('threshold_errors_error'      => 1,
            'threshold_errors_unstable'   => -1,
            'threshold_failures_error'    => -1,
            'threshold_failures_unstable' => 1
        );
        $model1                               = ORM::factory('Build');
        $model1->phpunit_globaldata->failures = 0;
        $model1->phpunit_globaldata->errors   = 0;
        $this->assertEquals('ok', $this->target->analyze($model1, $thresholds));

        $model2                               = ORM::factory('Build');
        $model2->phpunit_globaldata->failures = 1;
        $model2->phpunit_globaldata->errors   = 0;
        $this->assertEquals('unstable', $this->target->analyze($model2, $thresholds));

        $model3                               = ORM::factory('Build');
        $model3->phpunit_globaldata->failures = 0;
        $model3->phpunit_globaldata->errors   = 1;
        $this->assertEquals('error', $this->target->analyze($model3, $thresholds));

        $model4                               = ORM::factory('Build');
        $model4->phpunit_globaldata->failures = 1;
        $model4->phpunit_globaldata->errors   = 1;
        $this->assertEquals('error', $this->target->analyze($model4, $thresholds));
    }
}