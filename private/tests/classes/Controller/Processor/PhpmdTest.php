<?php

class Controller_Processor_PhpmdTest extends TestCase_Processor
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
     * @covers Controller_Processor_Phpmd::process
     * @covers Controller_Processor_Phpmd::findDeltas
     */
    public function testProcess()
    {
        $this->CopyReport(
                'html', dirname(__FILE__) . DIR_SEP . '_files' . DIR_SEP . 'phpmd-report.html'
        );

        $this->target->process($this->buildId);
        $this->commit();

        $globaldataExpected = array(array('errors' => 2, 'errors_delta' => -3));
        $globaldata         = DB::select('errors', 'errors_delta')
                        ->from('phpmd_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data5'])
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');
    }

    /**
     * @covers Controller_Processor_Phpmd::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $this->commit();
        $globaldata = DB::select('errors')
                        ->from('phpmd_globaldatas')
                        ->where('id', '!=', $this->genNumbers['data5'])
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }

    /**
     * @covers Controller_Processor_Phpmd::analyze
     */
    public function testAnalyze()
    {
        $build = ORM::factory('Build');
        $build->phpmd_globaldata->errors    = 9;

        $parameters = array(
            'threshold_errors_error'       => 10,
            'threshold_errors_unstable'    => 1,
        );
        $this->assertEquals(Owaka::BUILD_UNSTABLE, $this->target->analyze($build, $parameters));
    }
}