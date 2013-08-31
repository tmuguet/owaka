<?php

class Controller_Processor_CoverageTest extends TestCase_Processor
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
     * @covers Controller_Processor_Coverage::process
     * @covers Controller_Processor_Coverage::findDeltas
     */
    public function testProcess()
    {
        $this->CopyReport(
                'raw', dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'coverage-report.xml'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(
            array(
                'methodcount'             => 11,
                'methodscovered'          => 7,
                'methodcoverage'          => round(7 * 100 / 11, 2),
                'statementcount'          => 142,
                'statementscovered'       => 119,
                'statementcoverage'       => round(119 * 100 / 142, 2),
                'totalcount'              => 153,
                'totalcovered'            => 126,
                'totalcoverage'           => round(126 * 100 / 153, 2),
                'methodcoverage_delta'    => round(7 * 100 / 11, 2) - 60,
                'statementcoverage_delta' => round(119 * 100 / 142, 2) - 77,
                'totalcoverage_delta'     => round(126 * 100 / 153, 2) - 76,
            )
        );
        $globaldata         = DB::select(
                                'methodcount', 'methodscovered', 'methodcoverage', 'statementcount',
                                'statementscovered', 'statementcoverage', 'totalcount', 'totalcovered', 'totalcoverage',
                                'methodcoverage_delta', 'statementcoverage_delta', 'totalcoverage_delta'
                        )
                        ->from('coverage_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data3'])
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');
    }

    /**
     * @covers Controller_Processor_Coverage::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $globaldata = DB::select('methodcount')
                        ->from('coverage_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data3'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }
}