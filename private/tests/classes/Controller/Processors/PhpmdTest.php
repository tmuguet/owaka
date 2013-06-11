<?php

class Controller_Processors_PhpmdTest extends TestCase_Processors
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
     * @covers Controller_Processors_Phpmd::process
     */
    public function testProcess()
    {
        $this->CopyReport(
                'html', dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'phpmd-report.html'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(array('errors' => 2));
        $globaldata         = DB::select('errors')
                        ->from('phpmd_globaldatas')
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');
    }

    /**
     * @covers Controller_Processors_Phpmd::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $globaldata = DB::select('errors')
                        ->from('phpmd_globaldatas')
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }
}