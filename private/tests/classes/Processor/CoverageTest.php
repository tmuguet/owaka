<?php

class Processor_CoverageTest extends TestCase_Processor
{

    public function setUp()
    {
        parent::setUp();

        $this->build = ORM::factory('Build', $this->genNumbers['build2']);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers Processor_Coverage::process
     * @covers Processor_Coverage::findDeltas
     */
    public function testProcess()
    {
        $this->CopyReport(
                'raw', dirname(__FILE__) . DIR_SEP . '_files' . DIR_SEP . 'coverage-report.xml'
        );

        $this->target->process($this->build);
        $this->commit();

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
     * @covers Processor_Coverage::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->build);
        $this->commit();
        $globaldata = DB::select('methodcount')
                        ->from('coverage_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data3'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }

    /**
     * @covers Processor_Coverage::analyze
     */
    public function testAnalyze()
    {
        $build = ORM::factory('Build');
        $build->coverage_globaldata->methodcoverage    = 10;
        $build->coverage_globaldata->statementcoverage = 10;
        $build->coverage_globaldata->totalcoverage     = 10;

        $parameters = array(
            'threshold_methodcoverage_error'       => -1,
            'threshold_statementcoverage_error'    => 100,
            'threshold_totalcoverage_error'        => -1,
            'threshold_methodcoverage_unstable'    => -1,
            'threshold_statementcoverage_unstable' => -1,
            'threshold_totalcoverage_unstable'     => -1,
            'threshold_methodcoverage_delta_error'       => -1,
            'threshold_statementcoverage_delta_error'    => -1,
            'threshold_totalcoverage_delta_error'        => -1,
            'threshold_methodcoverage_delta_unstable'    => -1,
            'threshold_statementcoverage_delta_unstable' => -1,
            'threshold_totalcoverage_delta_unstable'     => -1,
        );
        $this->assertEquals(Owaka::BUILD_ERROR, $this->target->analyze($build, $parameters));
    }
}