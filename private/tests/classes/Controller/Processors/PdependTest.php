<?php

class Controller_Processors_PdependTest extends TestCase_Processors
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
     * @covers Controller_Processors_Pdepend::process
     */
    public function testProcess()
    {
        $this->CopyReport(
                'summary',
                dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'pdepend-summary.xml'
        );

        $this->target->process($this->buildId);

        $globaldataExpected = array(
            array(
                'ahh'    => 2.6,
                'andc'   => 0.8392,
                'calls'  => 525,
                'ccn'    => 402,
                'ccn2'   => 444,
                'cloc'   => 1143,
                'clsa'   => 8,
                'clsc'   => 44,
                'eloc'   => 2623,
                'fanout' => 75,
                'leafs'  => 40,
                'lloc'   => 1283,
                'loc'    => 4194,
                'maxdit' => 6,
                'ncloc'  => 3051,
                'noc'    => 52,
                'nof'    => 0,
                'noi'    => 1,
                'nom'    => 168,
                'nop'    => 9,
                'roots'  => 4
            )
        );
        $globaldata         = DB::select(
                                'ahh', 'andc', 'calls', 'ccn', 'ccn2', 'cloc', 'clsa', 'clsc', 'eloc', 'fanout',
                                'leafs', 'lloc', 'loc', 'maxdit', 'ncloc', 'noc', 'nof', 'noi', 'nom', 'nop', 'roots'
                        )
                        ->from('pdepend_globaldatas')
                        ->execute()->as_array();
        $this->assertEquals($globaldataExpected, $globaldata, 'Bad data inserted');
    }

    /**
     * @covers Controller_Processors_Pdepend::process
     */
    public function testProcessEmpty()
    {
        $this->target->process($this->buildId);
        $globaldata = DB::select('ahh')
                        ->from('pdepend_globaldatas')
                        ->execute()->as_array();
        $this->assertEmpty($globaldata, 'Data inserted');
    }
}