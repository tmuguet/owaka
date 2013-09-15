<?php

class Controller_Processor_CodesnifferTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build2'];
        $this->target->request->setParam('id', $this->buildId);
    }

    /**
     * @covers Controller_Processor_Codesniffer::process
     * @covers Controller_Processor_Codesniffer::findRegressions
     */
    public function testProcess()
    {
        $this->CopyReport(
                'xml',
                dirname(__FILE__) . DIR_SEP . '_files' . DIR_SEP . 'codesniffer-report.xml'
        );

        $this->target->process($this->buildId);
        $this->commit();

        $globaldataExpected = array(
            array(
                'warnings'             => 1,
                'errors'               => 4,
                'warnings_regressions' => 1,
                'errors_regressions'   => 3,
                'warnings_fixed'       => 1,
                'errors_fixed'         => 1
            )
        );
        $globaldata         = DB::select(
                                'warnings', 'errors', 'warnings_regressions', 'errors_regressions', 'warnings_fixed',
                                'errors_fixed'
                        )
                        ->from('codesniffer_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data2'])
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');

        $dataExpected = array(
            array('file'       => 'file1', 'message'    => 'message1', 'line'       => 6, 'severity'   => 'error', 'regression' => 1),
            array('file'       => 'file1', 'message'    => 'message2', 'line'       => 42, 'severity'   => 'error', 'regression' => 0),
            array('file'       => 'file1', 'message'    => 'message3', 'line'       => 8, 'severity'   => 'error', 'regression' => 1),
            array('file'       => 'file2', 'message'    => 'message', 'line'       => 10, 'severity'   => 'error', 'regression' => 1),
            array('file'       => 'file2', 'message'    => 'message4', 'line'       => 1, 'severity'   => 'warning', 'regression' => 1),
        );
        $data         = DB::select('file', 'message', 'line', 'severity', 'regression')
                        ->from('codesniffer_errors')
                        ->where('id', '!=', $this->genNumbers['data1'])
                        ->where('id', '!=', $this->genNumbers['data8'])
                        ->where('id', '!=', $this->genNumbers['data9'])
                        ->order_by('id', 'ASC')
                        ->execute()->as_array();
        $this->assertEquals($dataExpected, $data, 'Bad data inserted');
    }

    /**
     * @covers Controller_Processor_Codesniffer::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $this->commit();
        $globaldata = DB::select('warnings', 'errors')
                        ->from('codesniffer_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data2'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }

    /**
     * @covers Controller_Processor_Codesniffer::analyze
     */
    public function testAnalyze()
    {
        $build = ORM::factory('Build');
        $build->codesniffer_globaldata->errors = 10;
        $build->codesniffer_globaldata->warnings = 0;

        $parameters = array(
            'threshold_errors_error'      => -1,
            'threshold_warnings_error'    => -1,
            'threshold_errors_unstable'   => 1,
            'threshold_warnings_unstable' => -1,
        );
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $this->target->analyze($build, $parameters));
    }
}