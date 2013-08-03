<?php

class Controller_Processor_CodesnifferTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->buildId = $this->genNumbers['build1'];
        $this->target->request->setParam('id', $this->buildId);
    }

    /**
     * @covers Controller_Processor_Codesniffer::process
     */
    public function testProcess()
    {
        $this->CopyReport(
                'xml',
                dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'codesniffer-report.xml'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(array('warnings' => 1, 'errors'   => 3));
        $globaldata         = DB::select('warnings', 'errors')
                        ->from('codesniffer_globaldatas')
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');

        $dataExpected = array(
            array('file'     => 'file1', 'message'  => 'message1', 'line'     => 6, 'severity' => 'error'),
            array('file'     => 'file1', 'message'  => 'message2', 'line'     => 42, 'severity' => 'error'),
            array('file'     => 'file1', 'message'  => 'message3', 'line'     => 8, 'severity' => 'error'),
            array('file'     => 'file2', 'message'  => 'message4', 'line'     => 1, 'severity' => 'warning'),
        );
        $data         = DB::select('file', 'message', 'line', 'severity')
                        ->from('codesniffer_errors')
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
        $globaldata = DB::select('warnings', 'errors')
                        ->from('codesniffer_globaldatas')
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }
}